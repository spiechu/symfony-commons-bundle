<?php

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SpiechuSymfonyCommonsExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws OutOfBoundsException
     * @throws ServiceNotFoundException
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processedConfig = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->processGetMethodOverride($loader, $container, $processedConfig['get_method_override']);
        $this->processResponseSchemaValidation($loader, $container, $processedConfig['response_schema_validation']);
        $this->processApiVersioning($loader, $container, $processedConfig['api_versioning']);

        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug_services.xml');
        }
    }

    /**
     * @param XmlFileLoader $loader
     * @param ContainerBuilder $container
     * @param array $options
     *
     * @throws \Exception
     */
    protected function processGetMethodOverride(XmlFileLoader $loader, ContainerBuilder $container, array $options): void
    {
        if (!$options['enabled']) {
            return;
        }

        if ('spiechu_symfony_commons.event_listener.get_method_override_listener' === $options['listener_service_id']) {
            $loader->load('get_method_override_listener.xml');
        }

        $getMethodOverrideListenerDefinition = $container->getDefinition($options['listener_service_id']);

        $this->addOrReplaceDefinitionArgument($getMethodOverrideListenerDefinition, 0, $options['query_param_name']);
        $this->addOrReplaceDefinitionArgument($getMethodOverrideListenerDefinition, 1, $options['allow_methods_override']);
    }

    /**
     * @param XmlFileLoader $loader
     * @param ContainerBuilder $container
     * @param array $options
     *
     * @throws \Exception
     */
    protected function processResponseSchemaValidation(XmlFileLoader $loader, ContainerBuilder $container, array $options): void
    {
        if (!$options['enabled']) {
            return;
        }

        $loader->load('response_schema_validation_listeners.xml');

        $this->addOrReplaceDefinitionArgument(
            $container->getDefinition('spiechu_symfony_commons.event_listener.response_schema_validator_listener'),
            1,
            $options['throw_exception_when_format_not_found']
        );

        if ('spiechu_symfony_commons.event_listener.failed_schema_check_listener' !== $options['failed_schema_check_listener_service_id']) {
            $this->clearListenerTags($container->getDefinition('spiechu_symfony_commons.event_listener.failed_schema_check_listener'));
        }

        if ($options['disable_json_check_schema_subscriber']) {
            $this->clearListenerTags($container->getDefinition('spiechu_symfony_commons.event_listener.json_check_schema_subscriber'));
        }
    }

    /**
     * @param XmlFileLoader $loader
     * @param ContainerBuilder $container
     * @param array $options
     *
     * @throws \Exception
     */
    protected function processApiVersioning(XmlFileLoader $loader, ContainerBuilder $container, array $options): void
    {
        if (!$options['enabled']) {
            return;
        }

        $loader->load('api_versioning_listeners.xml');

        if (!$options['versioned_view_listener']) {
            $this->clearListenerTags($container->getDefinition('spiechu_symfony_commons.event_listener.versioned_view_listener'));
        }
    }

    /**
     * @param Definition $definition
     * @param int $index
     * @param $value
     *
     * @throws OutOfBoundsException
     */
    protected function addOrReplaceDefinitionArgument(Definition $definition, int $index, $value): void
    {
        if (array_key_exists($index, $definition->getArguments())) {
            $definition->replaceArgument($index, $value);
        } else {
            $definition->setArgument($index, $value);
        }
    }

    /**
     * @param Definition $definition
     */
    protected function clearListenerTags(Definition $definition): void
    {
        $definition->clearTag('kernel.event_subscriber');
        $definition->clearTag('kernel.event_listener');

        $definition->setPublic(false);
    }
}
