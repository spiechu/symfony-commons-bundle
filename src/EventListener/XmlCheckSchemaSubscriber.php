<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\SchemaValidatorFactoryInterface;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\XmlSchemaValidatorFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class XmlCheckSchemaSubscriber extends AbstractCheckSchemaSubscriber implements EventSubscriberInterface
{
    /**
     * @var XmlSchemaValidatorFactory
     */
    protected $xmlSchemaValidatorFactory;

    /**
     * @param XmlSchemaValidatorFactory $xmlSchemaValidatorFactory
     */
    public function __construct(XmlSchemaValidatorFactory $xmlSchemaValidatorFactory)
    {
        $this->xmlSchemaValidatorFactory = $xmlSchemaValidatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::getCheckSchemaEventNameFor('xml') => 'validateSchema',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSchemaValidatorFactory(): SchemaValidatorFactoryInterface
    {
        return $this->xmlSchemaValidatorFactory;
    }
}
