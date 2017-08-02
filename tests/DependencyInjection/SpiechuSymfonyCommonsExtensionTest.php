<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\DependencyInjection;

use Spiechu\SymfonyCommonsBundle\DependencyInjection\SpiechuSymfonyCommonsExtension;
use Spiechu\SymfonyCommonsBundle\EventListener\GetMethodOverrideListener;
use Spiechu\SymfonyCommonsBundle\EventListener\RequestSchemaValidatorListener;
use Spiechu\SymfonyCommonsBundle\EventListener\ResponseSchemaValidatorListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SpiechuSymfonyCommonsExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var SpiechuSymfonyCommonsExtension
     */
    protected $extension;

    public function testNoListenersSetUpOnDefaultConfiguration()
    {
        $config = [
            'spiechu_symfony_commons' => [],
        ];

        $this->extension->load($config, $this->container);

        foreach (array_keys($this->container->getDefinitions()) as $id) {
            self::assertNotRegExp('/^spiechu_symfony_commons\..{1,}_(listener|subscriber)$/i', $id);
        }
    }

    public function testGetMethodOverrideListenerPresentWhenEnabled()
    {
        $config = [
            'spiechu_symfony_commons' => [
                'get_method_override' => [
                    'enabled' => true,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.get_method_override_listener');
        self::assertSame(GetMethodOverrideListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());
    }

    public function testResponseSchemaListenersPresentWhenEnabled()
    {
        $config = [
            'spiechu_symfony_commons' => [
                'response_schema_validation' => [
                    'enabled' => true,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.request_schema_validator_listener');
        self::assertSame(RequestSchemaValidatorListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.response_schema_validator_listener');
        self::assertSame(ResponseSchemaValidatorListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());
    }

    public function testModifiableListenerArguments()
    {
        $config = [
            'spiechu_symfony_commons' => [
                'get_method_override' => [
                    'enabled' => true,
                    'query_param_name' => 'test_query_param_name',
                    'allow_methods_override' => 'put',
                ],
                'response_schema_validation' => [
                    'enabled' => true,
                    'throw_exception_when_format_not_found' => false,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.get_method_override_listener');
        self::assertSame('test_query_param_name', $listenerDefinition->getArgument(0));
        self::assertSame(['PUT'], $listenerDefinition->getArgument(1));

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.response_schema_validator_listener');
        self::assertFalse($listenerDefinition->getArgument(1));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->container = new ContainerBuilder();
        $this->extension = new SpiechuSymfonyCommonsExtension();
    }
}
