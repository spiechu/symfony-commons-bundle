<?php

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
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
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processedConfig = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->processGetMethodOverride($loader, $container, $processedConfig['get_method_override']);
        $this->processResponseSchemaValidation($loader, $container, $processedConfig['response_schema_validation']);
    }

    protected function processGetMethodOverride(XmlFileLoader $loader, ContainerBuilder $container, array $options): void
    {
        if (!$options['enabled']) {
            return;
        }

        $getMethodOverrideListenerDefinition = $container->getDefinition($options['listener_service_id']);

        if ('spiechu_symfony_commons.event_listener.get_method_override_listener' === $options['listener_service_id']) {
            $loader->load('get_method_override_listener.xml');
        }

        $this->addOrReplaceDefinitionArgument($getMethodOverrideListenerDefinition, 0, $options['query_param_name']);
        $this->addOrReplaceDefinitionArgument($getMethodOverrideListenerDefinition, 1, $options['allow_methods_override']);
    }

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

    }

    protected function addOrReplaceDefinitionArgument(Definition $definition, int $index, $value): void
    {
        if (array_key_exists($index, $definition->getArguments())) {
            $definition->replaceArgument($index, $value);
        } else {
            $definition->setArgument($index, $value);
        }
    }
}
