<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Twig;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataCollectorExtension extends AbstractExtension
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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('schema_file_exists', [$this, 'schemaFileExists']),
        ];
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
