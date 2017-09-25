<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Test\Functional\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass(): string
    {
        require_once __DIR__ . '/app/AppKernel.php';

        return AppKernel::class;
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = array()): AppKernel
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        return new $class(
            static::getVarDir(),
            $options['test_case'],
            $options['root_config'] ?? 'config.yml',
            $options['environment'] ?? strtolower(static::getVarDir() . $options['test_case']),
            $options['debug'] ?? true
        );
    }


    protected static function getVarDir(): string
    {
        return 'SC' . substr(strrchr(static::class, '\\'), 1);
    }
}
