<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JmsSerializerCompatibilityPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('jms_serializer.stopwatch_subscriber')) {
            $definition = $container->getDefinition('jms_serializer.stopwatch_subscriber');

            if (!$definition->isPublic()) {
                $definition->setPublic(true);
            }
        }
    }
}
