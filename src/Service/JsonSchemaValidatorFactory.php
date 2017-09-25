<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use JsonSchema\SchemaStorage;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;

class JsonSchemaValidatorFactory
{
    /**
     * @var FileLocatorInterface
     */
    protected $fileLocator;

    /**
     * @var array
     */
    protected $registeredSchemas;

    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        $this->registeredSchemas = [];
    }

    /**
     * @throws \RuntimeException When schema with provided $id is already registered
     */
    public function registerSchema(string $id, string $schemaResourceLocation): self
    {
        if ($this->hasSchema($id)) {
            throw new \RuntimeException(sprintf('Schema with id "%s" already registered.', $id));
        }

        $this->registeredSchemas[$id] = $schemaResourceLocation;

        return $this;
    }

    public function hasSchema(string $id): bool
    {
        return array_key_exists($id, $this->registeredSchemas);
    }

    /**
     * @throws FileLocatorFileNotFoundException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getValidator(string $id): JsonSchemaValidator
    {
        if (!$this->hasSchema($id)) {
            throw new \RuntimeException(sprintf('Schema with id "%s" is not registered.', $id));
        }

        if (!$this->registeredSchemas[$id] instanceof JsonSchemaValidator) {
            $this->registeredSchemas[$id] = new JsonSchemaValidator($this->createSchema($id), new Validator());
        }

        return $this->registeredSchemas[$id];
    }

    /**
     * @throws FileLocatorFileNotFoundException
     * @throws \InvalidArgumentException
     */
    protected function createSchema(string $id): \stdClass
    {
        $mainSchemaFilePath = $this->fileLocator->locate($this->registeredSchemas[$id]);
        $schemaUri = basename($mainSchemaFilePath);
        $schemaBaseUri = sprintf('file://%s/%s', dirname($mainSchemaFilePath), $schemaUri);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve($schemaUri, $schemaBaseUri);

        $schemaStorage = new SchemaStorage($retriever);
        $schemaStorage->addSchema($schemaBaseUri, $schema);

        return $schema;
    }
}
