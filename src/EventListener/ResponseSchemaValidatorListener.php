<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Commands;
use Spiechu\SymfonyCommonsBundle\Utils\ArrayUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseSchemaValidatorListener
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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

        // not able to determine format
        if (null === $format) {
            return;
        }

        $format = strtolower($format);

        if (empty($responseSchemas[$format])) {
            return;
        }

        if (array_key_exists($responseStatusCode = $response->getStatusCode(), $responseSchemas[$format])) {
            $this->dispatchCheckSchemaRequest($format, $response->getContent(), $responseSchemas[$format][$responseStatusCode]);
        }
    }

    /**
     * @throws \RuntimeException When no listener was able to check request
     * @throws \RuntimeException When schema violations
     */
    protected function dispatchCheckSchemaRequest(string $format, string $content, string $responseSchemaLocation): void
    {
        $checkRequest = new CheckRequest($format, $content, $responseSchemaLocation);

        $this->eventDispatcher->dispatch(Commands::getCheckSchemaEventNameFor($format), $checkRequest);

        if (!$checkRequest->wasChecked()) {
            throw new \RuntimeException(sprintf('No listener was able to check request for format "%s"', $format));
        }

        if (!empty($schemaViolations = $checkRequest->getSchemaViolations())) {
            throw new \RuntimeException(sprintf(
                'Schema violations for "%s": "%s"',
                $format,
                implode(', ', $schemaViolations)
            ));
        }
    }
}
