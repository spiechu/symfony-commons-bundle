<?php

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection;

use function foo\func;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('spiechu_symfony_commons');

        $this->addGetMethodOverride($rootNode);
        $this->addResponseSchemaValidation($rootNode);
        $this->addApiVersionSupport($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function addGetMethodOverride(ArrayNodeDefinition $rootNode): void
    {
        $overridableHttpMethods = $this->getOverridableHttpMethods();
        $defaultOverridedHttpMethods = [
            Request::METHOD_DELETE,
            Request::METHOD_POST,
            Request::METHOD_PUT,
        ];

        $rootNode
            ->children()
                ->arrayNode('get_method_override')
                    ->addDefaultsIfNotSet()
                    ->canBeEnabled()
                    ->children()
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
                            ->defaultValue($defaultOverridedHttpMethods)
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
                                ->then(function (array $methods): array {
                                    return array_unique(array_map('strtoupper', $methods));
                                })
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    protected function addResponseSchemaValidation(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('response_schema_validation')
                    ->addDefaultsIfNotSet()
                    ->canBeEnabled()
                    ->children()
                        ->booleanNode('throw_exception_when_format_not_found')->defaultTrue()->end()
                        ->scalarNode('failed_schema_check_listener_service_id')
                            ->defaultValue('spiechu_symfony_commons.event_listener.failed_schema_check_listener')
                        ->end()
                        ->booleanNode('disable_json_check_schema_subscriber')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function addApiVersionSupport(ArrayNodeDefinition $rootNode): void
    {
        $versionNormalizer = static function($version): string {
            if (is_string($version)) {
                return $version;
            }

            if (!is_numeric($version)) {
                throw new \InvalidArgumentException(sprintf('"%s" is not numeric', $version));
            }

            return number_format($version, 1, '.', '');
        };

        $rootNode
            ->children()
                ->arrayNode('api_versioning')
                    ->addDefaultsIfNotSet()
                    ->canBeEnabled()
                    ->children()
                        ->booleanNode('versioned_view_listener')->defaultFalse()->end()
                        ->arrayNode('features')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('since')
                                        ->defaultNull()
                                        ->beforeNormalization()
                                            ->always()
                                            ->then($versionNormalizer)
                                        ->end()
                                    ->end()
                                    ->scalarNode('until')
                                        ->defaultNull()
                                        ->beforeNormalization()
                                            ->always()
                                            ->then($versionNormalizer)
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * @return string[]
     */
    protected function getOverridableHttpMethods(): array
    {
        return [
            Request::METHOD_CONNECT,
            Request::METHOD_DELETE,
            Request::METHOD_HEAD,
            Request::METHOD_OPTIONS,
            Request::METHOD_PATCH,
            Request::METHOD_POST,
            Request::METHOD_PURGE,
            Request::METHOD_TRACE,
            Request::METHOD_PUT,
        ];
    }
}
