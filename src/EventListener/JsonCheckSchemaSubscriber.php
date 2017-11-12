<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\JsonSchemaValidatorFactory;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\SchemaValidatorFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JsonCheckSchemaSubscriber extends AbstractCheckSchemaSubscriber implements EventSubscriberInterface
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
     * {@inheritdoc}
     */
    protected function getSchemaValidatorFactory(): SchemaValidatorFactoryInterface
    {
        return $this->jsonSchemaValidatorFactory;
    }
}
