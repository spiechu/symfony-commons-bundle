<?php

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('spiechu_symfony_commons');

        $this->addGetMethodOverride($rootNode);

        return $treeBuilder;
    }

    protected function addGetMethodOverride(ArrayNodeDefinition $rootNode): void
    {
        $overridableHttpMethods = ['PUT', 'DELETE'];

        $rootNode
            ->children()
                ->arrayNode('get_method_override')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('listener_service_id')
                            ->cannotBeEmpty()
                            ->defaultValue('spiechu_symfony_commons.event_listener.get_method_override_listener')
                        ->end()
                        ->scalarNode('query_param_name')
                            ->cannotBeEmpty()
                            ->defaultValue('_method')
                            ->validate()
                                ->ifTrue(function ($methodName): bool {
                                    return !is_string($methodName);
                                })
                                ->thenInvalid('Not a string provided')
                            ->end()
                        ->end()
                        ->arrayNode('allow_methods_override')
                            ->beforeNormalization()
                                ->ifString()->castToArray()
                            ->end()
                            ->defaultValue($overridableHttpMethods)
                            ->prototype('scalar')
                                ->validate()
                                    ->ifNotInArray($overridableHttpMethods)
                                    ->thenInvalid(sprintf(
                                        'Invalid methods to override provided, known are: "%s"',
                                        implode(', ', $overridableHttpMethods)
                                    ))
                                ->end()
                            ->end()
                            ->beforeNormalization()
                                ->ifArray()
                                ->then(function(array $methods): array {
                                    return array_unique(array_map('strtoupper', $methods));
                                })
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}
