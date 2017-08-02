<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\DependencyInjection;

use Spiechu\SymfonyCommonsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Processor
     */
    protected $processor;

    public function testConfigurationLoadsProperlyOnEmptyConfig(): void
    {
        $config = $this->processor->processConfiguration(
            $this->configuration,
            []
        );

        self::assertNotEmpty($config['get_method_override']);
        self::assertNotEmpty($config['response_schema_validation']);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }
}
