<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection\Compiler;

use Spiechu\SymfonyCommonsBundle\DependencyInjection\Utils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverridingListenersSetupPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('get_method_override_listener_service_id')) {
            $newListener = $container->getDefinition($container->getParameter('get_method_override_listener_service_id'));
            $originalListener = $container->getDefinition('spiechu_symfony_commons.event_listener.get_method_override_listener');

            Utils::addOrReplaceDefinitionArgument($newListener, 0, $originalListener->getArgument(0));
            Utils::addOrReplaceDefinitionArgument($newListener, 1, $originalListener->getArgument(1));
        }
    }
}
