<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

class XmlSchemaValidatorFactory extends AbstractSchemaValidatorFactory implements SchemaValidatorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getValidator(string $id): SchemaValidatorInterface
    {
        $this->assertHasSchema($id);

        if (!$this->registeredSchemas[$id] instanceof XmlSchemaValidator) {
            $this->registeredSchemas[$id] = new XmlSchemaValidator($this->fileLocator->locate($this->registeredSchemas[$id]));
        }

        return $this->registeredSchemas[$id];
    }
}
