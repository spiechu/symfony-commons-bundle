<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Commands;
use Spiechu\SymfonyCommonsBundle\Service\JsonSchemaValidatorFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JsonCheckSchemaSubscriber implements EventSubscriberInterface
{
    /**
     * @var JsonSchemaValidatorFactory
     */
    protected $jsonSchemaValidatorFactory;

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
            Commands::getCheckSchemaEventNameFor('json') => 'validateSchema',
        ];
    }

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
