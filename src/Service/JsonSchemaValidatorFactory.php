<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use JsonSchema\SchemaStorage;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

class JsonSchemaValidatorFactory extends AbstractSchemaValidatorFactory
{
    /**
     * @param string $id
     *
     * @throws FileLocatorFileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return JsonSchemaValidator
     */
    public function getValidator(string $id): JsonSchemaValidator
    {
        $this->assertHasSchema($id);

        if (!$this->registeredSchemas[$id] instanceof JsonSchemaValidator) {
            $this->registeredSchemas[$id] = new JsonSchemaValidator($this->createSchema($id), new Validator());
        }

        return $this->registeredSchemas[$id];
    }

    /**
     * @param string $id
     *
     * @throws \InvalidArgumentException
     * @throws FileLocatorFileNotFoundException
     *
     * @return \stdClass
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
