<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional;

use Spiechu\SymfonyCommonsBundle\Service\DataCollector;
use Spiechu\SymfonyCommonsBundle\Test\Functional\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class WebTestCase extends BaseWebTestCase
{
    public static function setUpBeforeClass()
    {
        static::deleteTmpDir();
    }

    public static function tearDownAfterClass()
    {
        static::deleteTmpDir();
    }

    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass(): string
    {
        require_once __DIR__.'/app/AppKernel.php';

        return AppKernel::class;
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = []): AppKernel
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        return new $class(
            static::getVarDir(),
            $options['test_case'],
            $options['root_config'] ?? 'config.yml',
            $options['environment'] ?? strtolower(static::getVarDir().$options['test_case']),
            $options['debug'] ?? true
        );
    }

    protected static function deleteTmpDir()
    {
        if (!file_exists($dir = sys_get_temp_dir().'/'.static::getVarDir())) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    protected static function getVarDir(): string
    {
        return 'SC'.substr(strrchr(static::class, '\\'), 1);
    }

    /**
     * @param Client      $client
     * @param null|string $expectedApiVersion
     * @param int         $expectedKnownRouteResponseSchemaNumber
     * @param int         $expectedAllPotentialErrorsCount
     */
    protected static function assertDataCollectorContainsProperData(
        Client $client,
        ?string $expectedApiVersion,
        int $expectedKnownRouteResponseSchemaNumber,
        int $expectedAllPotentialErrorsCount
    ) {
        /** @var DataCollector $dataCollector */
        $dataCollector = $client->getContainer()->get('profiler')->get('spiechu_symfony_commons.data_collector');

        self::assertSame($expectedApiVersion, $dataCollector->getApiVersion());
        self::assertSame($expectedKnownRouteResponseSchemaNumber, $dataCollector->getKnownRouteResponseSchemaNumber());
        self::assertSame($expectedAllPotentialErrorsCount, $dataCollector->getAllPotentialErrorsCount());
    }

    /**
     * @param Client $client
     *
     * @return DataCollector
     */
    protected static function getDataCollector(Client $client): DataCollector
    {
        return $client->getProfile()->getCollector(DataCollector::COLLECTOR_NAME);
    }
}
