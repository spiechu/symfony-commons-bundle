<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Commands;
use Spiechu\SymfonyCommonsBundle\Service\JsonSchemaValidator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JsonCheckSchemaSubscriber implements EventSubscriberInterface
{
    /**
     * @var JsonSchemaValidator
     */
    protected $jsonSchemaValidator;

    public function __construct(JsonSchemaValidator $jsonSchemaValidator)
    {
        $this->jsonSchemaValidator = $jsonSchemaValidator;
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
        $checkRequest->markChecked();
    }
}
