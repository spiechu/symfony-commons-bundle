<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ViewListenerAliasProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener')) {
            $container->setAlias('sensio_framework_extra.view.listener', 'Sensio\Bundle\FrameworkExtraBundle\EventListener\TemplateListener');
        }
    }
}
