<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

class XmlSchemaValidatorFactory extends AbstractSchemaValidatorFactory
{
    /**
     * @param string $id
     *
     * @throws FileLocatorFileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return XmlSchemaValidator
     */
    public function getValidator(string $id): XmlSchemaValidator
    {
        $this->assertHasSchema($id);

        if (!$this->registeredSchemas[$id] instanceof XmlSchemaValidator) {
            $this->registeredSchemas[$id] = new XmlSchemaValidator($this->fileLocator->locate($this->registeredSchemas[$id]));
        }

        return $this->registeredSchemas[$id];
    }
}
