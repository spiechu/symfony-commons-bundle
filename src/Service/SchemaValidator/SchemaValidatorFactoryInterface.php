<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

interface SchemaValidatorFactoryInterface
{
    /**
     * @param string $id
     *
     * @throws FileLocatorFileNotFoundException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return SchemaValidatorInterface
     */
    public function getValidator(string $id): SchemaValidatorInterface;

    /**
     * @param string $id
     * @param string $schemaResourceLocation
     *
     * @throws \RuntimeException When schema with provided $id is already registered
     */
    public function registerSchema(string $id, string $schemaResourceLocation): void;

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasSchema(string $id): bool;
}
