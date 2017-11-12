<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Symfony\Component\Config\FileLocatorInterface;

abstract class AbstractSchemaValidatorFactory
{
    /**
     * @var FileLocatorInterface
     */
    protected $fileLocator;

    /**
     * @var array
     */
    protected $registeredSchemas;

    /**
     * @param FileLocatorInterface $fileLocator
     */
    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;

        $this->registeredSchemas = [];
    }

    /**
     * @param string $id
     * @param string $schemaResourceLocation
     *
     * @throws \RuntimeException When schema with provided $id is already registered
     *
     * @return self
     */
    public function registerSchema(string $id, string $schemaResourceLocation): self
    {
        if ($this->hasSchema($id)) {
            throw new \RuntimeException(sprintf('Schema with id "%s" already registered.', $id));
        }

        $this->registeredSchemas[$id] = $schemaResourceLocation;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasSchema(string $id): bool
    {
        return array_key_exists($id, $this->registeredSchemas);
    }

    /**
     * @param string $id
     *
     * @throws \RuntimeException When schema with id "%s" is not registered
     */
    protected function assertHasSchema(string $id): void
    {
        if (!$this->hasSchema($id)) {
            throw new \RuntimeException(sprintf('Schema with id "%s" is not registered.', $id));
        }
    }
}
