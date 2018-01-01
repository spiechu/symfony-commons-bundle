<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

use JsonSchema\SchemaStorage;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

class JsonSchemaValidatorFactory extends AbstractSchemaValidatorFactory implements SchemaValidatorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getValidator(string $id): SchemaValidatorInterface
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
        $schemaBaseUri = sprintf('file://%s/%s', \dirname($mainSchemaFilePath), $schemaUri);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve($schemaUri, $schemaBaseUri);

        $schemaStorage = new SchemaStorage($retriever);
        $schemaStorage->addSchema($schemaBaseUri, $schema);

        if (!$schema instanceof \stdClass) {
            throw new \InvalidArgumentException('Retriever result is not instance of \stdClass');
        }

        return $schema;
    }
}
