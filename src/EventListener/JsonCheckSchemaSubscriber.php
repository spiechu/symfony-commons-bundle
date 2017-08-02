<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Commands;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JsonCheckSchemaSubscriber implements EventSubscriberInterface
{
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
