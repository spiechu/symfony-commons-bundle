<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\Service\ValidationResult;
use Spiechu\SymfonyCommonsBundle\Utils\ArrayUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseSchemaValidatorListener
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var bool
     */
    protected $throwExceptionWhenFormatNotFound;

    public function __construct(EventDispatcherInterface $eventDispatcher, bool $throwExceptionWhenFormatNotFound)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->throwExceptionWhenFormatNotFound = $throwExceptionWhenFormatNotFound;
    }

    public function onKernelResponse(FilterResponseEvent $filterResponseEvent): void
    {
        if (!$filterResponseEvent->isMasterRequest()) {
            return;
        }

        $request = $filterResponseEvent->getRequest();
        $responseSchemas = $this->getResponseSchemas($request);

        if (empty($responseSchemas)) {
            return;
        }

        $response = $filterResponseEvent->getResponse();
        $format = $this->getFormat($request, $response);

        if (null === $format || empty($responseSchemas[$format])) {
            return;
        }

        $responseStatusCode = $response->getStatusCode();

        if (!array_key_exists($responseStatusCode, $responseSchemas[$format])) {
            return;
        }

        $content = $response->getContent();
        $validationResult = $this->dispatchCheckSchemaRequest($format, $content, $responseSchemas[$format][$responseStatusCode]);

        $this->eventDispatcher->dispatch(
            Events::CHECK_RESULT,
            new CheckResult($format, $content, $validationResult)
        );
    }

    protected function getResponseSchemas(Request $request): array
    {
        $responseSchemas = $request->attributes->get(
            RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS,
            []
        );

        if (empty($responseSchemas) || empty(ArrayUtils::flatArrayRecursive($responseSchemas))) {
            return [];
        }

        return $responseSchemas;
    }

    /**
     * @throws \RuntimeException When not able to determine response format on $this->throwExceptionWhenFormatNotFound flag true
     */
    protected function getFormat(Request $request, Response $response): ?string
    {
        $format = $request->getFormat($response->headers->get('content_type'));

        if (null !== $format) {
            return strtolower($format);
        }

        if ($this->throwExceptionWhenFormatNotFound) {
            throw new \RuntimeException('Not able to determine response format');
        }

        return null;
    }

    /**
     * @throws \RuntimeException When no listener listens on check schema event
     * @throws \RuntimeException When no listener was able to check request
     * @throws \RuntimeException When schema violations
     */
    protected function dispatchCheckSchemaRequest(string $format, string $content, string $responseSchemaLocation): ValidationResult
    {
        $checkEventName = Events::getCheckSchemaEventNameFor($format);

        if (!$this->eventDispatcher->hasListeners($checkEventName)) {
            throw new \RuntimeException(sprintf('No listener listens on "%s" event', $checkEventName));
        }

        $checkRequest = new CheckRequest($format, $content, $responseSchemaLocation);

        $this->eventDispatcher->dispatch($checkEventName, $checkRequest);

        $validationResult = $checkRequest->getValidationResult();

        if (!$validationResult instanceof ValidationResult) {
            throw new \RuntimeException(sprintf('No listener was able to check request for format "%s"', $format));
        }

        return $validationResult;
    }
}
