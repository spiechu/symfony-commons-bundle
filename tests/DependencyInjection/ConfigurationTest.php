<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\DependencyInjection;

use Spiechu\SymfonyCommonsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testConfigurationLoadsProperlyOnEmptyConfig(): void
    {
        $config = $this->processor->processConfiguration(
            $this->configuration,
            []
        );

        self::assertNotEmpty($config['get_method_override']);
        self::assertNotEmpty($config['response_schema_validation']);
    }

    public function testConfigurationNormalizesHttpMethodsToUppercase()
    {
        $config = $this->processor->processConfiguration(
            $this->configuration,
            [
                'spiechu_symfony_commons' => [
                    'get_method_override' => [
                        'allow_methods_override' => [
                            'put', 'delete',
                        ],
                    ],
                ],
            ]
        );

        self::assertSame(['PUT', 'DELETE'], $config['get_method_override']['allow_methods_override']);
    }

    public function testConfigurationWillRejectUnknownHttpMethod()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageRegExp('/invalid methods/i');

        $this->processor->processConfiguration(
            $this->configuration,
            [
                'spiechu_symfony_commons' => [
                    'get_method_override' => [
                        'allow_methods_override' => [
                            'PUT', 'b',
                        ],
                    ],
                ],
            ]
        );
    }
}
