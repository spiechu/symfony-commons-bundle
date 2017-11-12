<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\Service\XmlSchemaValidatorFactory;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class XmlCheckSchemaSubscriber implements EventSubscriberInterface
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
     * @param CheckRequest $checkRequest
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws FileLocatorFileNotFoundException
     */
    public function validateSchema(CheckRequest $checkRequest): void
    {
        $schemaLocation = $checkRequest->getResponseSchemaLocation();

        if (!$this->xmlSchemaValidatorFactory->hasSchema($schemaLocation)) {
            $this->xmlSchemaValidatorFactory->registerSchema($schemaLocation, $schemaLocation);
        }

        $schemaValidator = $this->xmlSchemaValidatorFactory->getValidator($schemaLocation);

        $checkRequest->setValidationResult($schemaValidator->validate($checkRequest->getContent()));
    }
}
