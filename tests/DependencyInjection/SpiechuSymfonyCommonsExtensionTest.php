<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\DependencyInjection;

use Spiechu\SymfonyCommonsBundle\DependencyInjection\SpiechuSymfonyCommonsExtension;
use Spiechu\SymfonyCommonsBundle\EventListener\ApiVersionListener;
use Spiechu\SymfonyCommonsBundle\EventListener\ApiVersionProviderListener;
use Spiechu\SymfonyCommonsBundle\EventListener\FailedSchemaCheckListener;
use Spiechu\SymfonyCommonsBundle\EventListener\GetMethodOverrideListener;
use Spiechu\SymfonyCommonsBundle\EventListener\JsonCheckSchemaSubscriber;
use Spiechu\SymfonyCommonsBundle\EventListener\RequestSchemaValidatorListener;
use Spiechu\SymfonyCommonsBundle\EventListener\ResponseSchemaValidatorListener;
use Spiechu\SymfonyCommonsBundle\EventListener\VersionedViewListener;
use Spiechu\SymfonyCommonsBundle\Service\DataCollector;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

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

    /**
     * @var bool
     */
    protected $kernelDebug = false;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->container = new ContainerBuilder();

        $this->container->setParameter('kernel.debug', $this->kernelDebug);

        $this->extension = new SpiechuSymfonyCommonsExtension();
    }

    public function testNoListenersSetUpOnDefaultConfiguration(): void
    {
        $config = [
            'spiechu_symfony_commons' => [],
        ];

        $this->extension->load($config, $this->container);

        foreach (array_keys($this->container->getDefinitions()) as $id) {
            self::assertNotRegExp('/^spiechu_symfony_commons\..{1,}_(listener|subscriber)$/i', $id);
        }
    }

    public function testGetMethodOverrideListenerPresentWhenEnabled(): void
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
        self::assertSame('kernel.request', $listenerDefinition->getTags()['kernel.event_listener'][0]['event']);
    }

    public function testResponseSchemaListenersPresentWhenEnabled(): void
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
        self::assertSame('kernel.controller', $listenerDefinition->getTags()['kernel.event_listener'][0]['event']);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.response_schema_validator_listener');
        self::assertSame(ResponseSchemaValidatorListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());
        self::assertSame('kernel.response', $listenerDefinition->getTags()['kernel.event_listener'][0]['event']);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.failed_schema_check_listener');
        self::assertSame(FailedSchemaCheckListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());
        self::assertSame('spiechu_symfony_commons.event.response_schema_check.check_result', $listenerDefinition->getTags()['kernel.event_listener'][0]['event']);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.json_check_schema_subscriber');
        self::assertSame(JsonCheckSchemaSubscriber::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_subscriber', $listenerDefinition->getTags());
    }

    public function testJsonCheckSchemaSubscriberWontListenWhenDisabled(): void
    {
        $config = [
            'spiechu_symfony_commons' => [
                'response_schema_validation' => [
                    'enabled' => true,
                    'disable_json_check_schema_subscriber' => true,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.json_check_schema_subscriber');
        self::assertFalse($listenerDefinition->isPublic());
        self::assertEmpty($listenerDefinition->getTags());
    }

    public function testModifiableListenerArguments(): void
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

    public function testEmptyFailedSchemaCheckListener(): void
    {
        $config = [
            'spiechu_symfony_commons' => [
                'response_schema_validation' => [
                    'enabled' => true,
                    'failed_schema_check_listener_service_id' => null,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.failed_schema_check_listener');
        self::assertFalse($listenerDefinition->isPublic());
        self::assertEmpty($listenerDefinition->getTags());
    }

    public function testDataCollectorWillBePresentWhenDebug(): void
    {
        $config = [
            'spiechu_symfony_commons' => [],
        ];

        $this->extension->load($config, $this->container);
        self::assertFalse($this->container->hasDefinition('spiechu_symfony_commons.service.data_collector'));

        $this->kernelDebug = true;
        $this->setUp();
        $this->extension->load($config, $this->container);

        $definition = $this->container->getDefinition('spiechu_symfony_commons.service.data_collector');
        self::assertSame(DataCollector::class, $definition->getClass());

        $tags = $definition->getTags();
        self::assertArrayHasKey('kernel.event_subscriber', $tags);
        self::assertArrayHasKey('data_collector', $tags);
    }

    public function testExtensionWillReplaceExistingServiceArguments(): void
    {
        $fakeTestDefinition = new Definition(\stdClass::class, ['abc']);

        $this->container->setDefinition('fake_test_service', $fakeTestDefinition);

        $config = [
            'spiechu_symfony_commons' => [
                'get_method_override' => [
                    'enabled' => true,
                    'listener_service_id' => 'fake_test_service',
                ],
            ],
        ];

        self::assertSame('abc', $fakeTestDefinition->getArgument(0));

        $this->extension->load($config, $this->container);

        self::assertSame('_method', $fakeTestDefinition->getArgument(0));
    }

    public function testApiVersionListenersPresentWhenEnabled(): void
    {
        $config = [
            'spiechu_symfony_commons' => [
                'api_versioning' => [
                    'enabled' => true,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.api_version_listener');
        self::assertSame(ApiVersionListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());
        self::assertSame('kernel.controller', $listenerDefinition->getTags()['kernel.event_listener'][0]['event']);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.api_version_provider_listener');
        self::assertSame(ApiVersionProviderListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());
        self::assertSame('spiechu_symfony_commons.event.api_version.api_version_set', $listenerDefinition->getTags()['kernel.event_listener'][0]['event']);
    }

    public function testVersionedViewListenerNotPresentByDefault(): void
    {
        $config = [
            'spiechu_symfony_commons' => [
                'api_versioning' => [
                    'enabled' => true,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.versioned_view_listener');
        self::assertFalse($listenerDefinition->isPublic());
        self::assertEmpty($listenerDefinition->getTags());
    }

    public function testVersionedViewListenerEvent(): void
    {
        $config = [
            'spiechu_symfony_commons' => [
                'api_versioning' => [
                    'enabled' => true,
                    'versioned_view_listener' => true,
                ],
            ],
        ];

        $this->extension->load($config, $this->container);

        $listenerDefinition = $this->container->getDefinition('spiechu_symfony_commons.event_listener.versioned_view_listener');
        self::assertSame(VersionedViewListener::class, $listenerDefinition->getClass());
        self::assertArrayHasKey('kernel.event_listener', $listenerDefinition->getTags());
        self::assertSame('kernel.view', $listenerDefinition->getTags()['kernel.event_listener'][0]['event']);
    }
}
