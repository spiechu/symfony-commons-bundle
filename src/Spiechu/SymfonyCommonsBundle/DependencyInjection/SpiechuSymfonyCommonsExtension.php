<?php

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SpiechuSymfonyCommonsExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws OutOfBoundsException
     * @throws ServiceNotFoundException
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processedConfig = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->processGetMethodOverride($container, $processedConfig['get_method_override']);
    }

    protected function processGetMethodOverride(ContainerBuilder $container, array $options)
    {
        $getMethodOverrideListenerDefinition = $container->getDefinition($options['listener_service_id']);

        $this->addOrReplaceDefinitionArgument($getMethodOverrideListenerDefinition, 0, $options['query_param_name']);
        $this->addOrReplaceDefinitionArgument($getMethodOverrideListenerDefinition, 1, $options['allow_methods_override']);

        if (!$options['enabled']) {
            $getMethodOverrideListenerDefinition->clearTag('kernel.event_listener');
        }
    }

    protected function addOrReplaceDefinitionArgument(Definition $definition, int $index, $value)
    {
        if (array_key_exists($index, $definition->getArguments())) {
            $definition->replaceArgument($index, $value);
        } else {
            $definition->setArgument($index, $value);
        }
    }
}
