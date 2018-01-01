<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\SchemaFilesExistenceChecker;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class ControllerSchemaValidationCheckSubscriber implements CacheWarmerInterface
{
    /**
     * @var SchemaFilesExistenceChecker
     */
    protected $schemaFilesExistenceChecker;

    /**
     * @var bool
     */
    protected $isDebugMode;

    /**
     * @param SchemaFilesExistenceChecker $schemaFilesExistenceChecker
     * @param bool                        $isDebugMode
     */
    public function __construct(SchemaFilesExistenceChecker $schemaFilesExistenceChecker, bool $isDebugMode)
    {
        $this->schemaFilesExistenceChecker = $schemaFilesExistenceChecker;
        $this->isDebugMode = $isDebugMode;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional(): bool
    {
        return !$this->isDebugMode;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir): void
    {
        $this->schemaFilesExistenceChecker->checkControllerResponseSchemaValidatorFiles();
    }
}
