<?php

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        $this->processGetMethodOverride($loader, $container, $processedConfig['get_method_override']);
        $this->processResponseSchemaValidation($loader, $container, $processedConfig['response_schema_validation']);
        $this->processApiVersioning($loader, $container, $processedConfig['api_versioning']);

        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug_services.xml');
        }
    }

    /**
     * @param XmlFileLoader    $loader
     * @param ContainerBuilder $container
     * @param array            $options
     *
     * @throws \Exception
     */
    protected function processGetMethodOverride(XmlFileLoader $loader, ContainerBuilder $container, array $options): void
    {
        if (!$options['enabled']) {
            return;
        }

        $loader->load('get_method_override_listener.xml');

        $service = $container->getDefinition('spiechu_symfony_commons.event_listener.get_method_override_listener');

        Utils::addOrReplaceDefinitionArgument($service, 0, $options['query_param_name']);
        Utils::addOrReplaceDefinitionArgument($service, 1, $options['allow_methods_override']);

        if ('spiechu_symfony_commons.event_listener.get_method_override_listener' === $options['listener_service_id']) {
            return;
        }

        Utils::clearListenerTags($service);

        $container->setParameter('get_method_override_listener_service_id', $options['listener_service_id']);
    }

    /**
     * @param XmlFileLoader    $loader
     * @param ContainerBuilder $container
     * @param array            $options
     *
     * @throws \Exception
     */
    protected function processResponseSchemaValidation(XmlFileLoader $loader, ContainerBuilder $container, array $options): void
    {
        if (!$options['enabled']) {
            return;
        }

        $loader->load('response_schema_validation_listeners.xml');

        Utils::addOrReplaceDefinitionArgument(
            $container->getDefinition('spiechu_symfony_commons.event_listener.response_schema_validator_listener'),
            1,
            $options['throw_exception_when_format_not_found']
        );

        if ('spiechu_symfony_commons.event_listener.failed_schema_check_listener' !== $options['failed_schema_check_listener_service_id']) {
            Utils::clearListenerTags($container->getDefinition('spiechu_symfony_commons.event_listener.failed_schema_check_listener'));
        }

        if ($options['disable_json_check_schema_subscriber']) {
            Utils::clearListenerTags($container->getDefinition('spiechu_symfony_commons.event_listener.json_check_schema_subscriber'));
        }
        if ($options['disable_xml_check_schema_subscriber']) {
            Utils::clearListenerTags($container->getDefinition('spiechu_symfony_commons.event_listener.xml_check_schema_subscriber'));
        }
    }

    /**
     * @param XmlFileLoader    $loader
     * @param ContainerBuilder $container
     * @param array            $options
     *
     * @throws \Exception
     */
    protected function processApiVersioning(XmlFileLoader $loader, ContainerBuilder $container, array $options): void
    {
        if (!$options['enabled']) {
            return;
        }

        $loader->load('api_versioning_listeners.xml');

        if (!empty($options['features'])) {
            $featuresProviderDefinition = $container->getDefinition('spiechu_symfony_commons.service.api_version_features_provider');
            $featuresProviderDefinition->addMethodCall('addFeatures', [$options['features']]);
        }

        if (!$options['versioned_view_listener']) {
            Utils::clearListenerTags($container->getDefinition('spiechu_symfony_commons.event_listener.versioned_view_listener'));
        }
    }
}
