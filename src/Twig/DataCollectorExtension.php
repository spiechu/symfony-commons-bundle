<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Twig;

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
        return array(
            new TwigFunction('schema_file_exists', [$this, 'schemaFileExists']),
        );
    }

    public function schemaFileExists()
    {
        $s = null;
    }
}
