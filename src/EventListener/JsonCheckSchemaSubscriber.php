<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\Service\JsonSchemaValidatorFactory;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JsonCheckSchemaSubscriber implements EventSubscriberInterface
{
    /**
     * @var JsonSchemaValidatorFactory
     */
    protected $jsonSchemaValidatorFactory;

    /**
     * @param JsonSchemaValidatorFactory $jsonSchemaValidatorFactory
     */
    public function __construct(JsonSchemaValidatorFactory $jsonSchemaValidatorFactory)
    {
        $this->jsonSchemaValidatorFactory = $jsonSchemaValidatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::getCheckSchemaEventNameFor('json') => 'validateSchema',
        ];
    }

    /**
     * @param CheckRequest $checkRequest
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws FileLocatorFileNotFoundException
     */
    public function validateSchema(CheckRequest $checkRequest): void
    {
        $schemaLocation = $checkRequest->getResponseSchemaLocation();

        if (!$this->jsonSchemaValidatorFactory->hasSchema($schemaLocation)) {
            $this->jsonSchemaValidatorFactory->registerSchema($schemaLocation, $schemaLocation);
        }

        $schemaValidator = $this->jsonSchemaValidatorFactory->getValidator($schemaLocation);

        $checkRequest->setValidationResult($schemaValidator->validate($checkRequest->getContent()));
    }
}
