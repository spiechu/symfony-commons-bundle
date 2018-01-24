<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;

class SchemaLocator
{
    /**
     * @var FileLocatorInterface
     */
    protected $fileLocator;

    /**
     * @param FileLocatorInterface $fileLocator
     */
    public function __construct(FileLocatorInterface $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * @param string $schemaLocation
     *
     * @return bool
     */
    public function schemaFileExists(string $schemaLocation): bool
    {
        try {
            $this->fileLocator->locate($schemaLocation);

            return true;
        } catch (FileLocatorFileNotFoundException $e) {
            return false;
        }
    }
}
