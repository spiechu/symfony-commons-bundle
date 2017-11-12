<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckRequest;
use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\SchemaValidatorFactoryInterface;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

abstract class AbstractCheckSchemaSubscriber
{
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
        $schemaValidatorFactory = $this->getSchemaValidatorFactory();

        if (!$schemaValidatorFactory->hasSchema($schemaLocation)) {
            $schemaValidatorFactory->registerSchema($schemaLocation, $schemaLocation);
        }

        $schemaValidator = $schemaValidatorFactory->getValidator($schemaLocation);

        $checkRequest->setValidationResult($schemaValidator->validate($checkRequest->getContent()));
    }

    /**
     * @return SchemaValidatorFactoryInterface
     */
    abstract protected function getSchemaValidatorFactory(): SchemaValidatorFactoryInterface;
}
