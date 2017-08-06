<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\Service\ValidationResult;
use Spiechu\SymfonyCommonsBundle\Utils\ArrayUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

    public function onKernelResponse(FilterResponseEvent $filterResponseEvent)
    {
        if (!$filterResponseEvent->isMasterRequest()) {
            return;
        }

        $request = $filterResponseEvent->getRequest();
        $responseSchemas = $request->attributes->get(
            RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS,
            []
        );

        if (empty($responseSchemas) || empty(ArrayUtils::flatArrayRecursive($responseSchemas))) {
            return;
        }

        $response = $filterResponseEvent->getResponse();
        $format = $request->getFormat($response->headers->get('content_type'));

        if (null === $format) {
            if ($this->throwExceptionWhenFormatNotFound) {
                throw new \RuntimeException('Not able to determine response format');
            }

            return;
        }

        $format = strtolower($format);

        if (empty($responseSchemas[$format])) {
            return;
        }

        if (array_key_exists($responseStatusCode = $response->getStatusCode(), $responseSchemas[$format])) {
            $content = $response->getContent();
            $validationResult = $this->dispatchCheckSchemaRequest($format, $content, $responseSchemas[$format][$responseStatusCode]);

            $this->eventDispatcher->dispatch(
                Events::CHECK_RESULT,
                new CheckResult($format, $content, $validationResult)
            );
        }
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
