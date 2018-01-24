<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Twig;

use Spiechu\SymfonyCommonsBundle\Service\SchemaLocator;

class DataCollectorExtension extends \Twig_Extension
{
    /**
     * @var SchemaLocator
     */
    protected $schemaLocator;

    /**
     * @param SchemaLocator $schemaLocator
     */
    public function __construct(SchemaLocator $schemaLocator)
    {
        $this->schemaLocator = $schemaLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('schema_file_exists', [$this->schemaLocator, 'schemaFileExists']),
        ];
    }
}
