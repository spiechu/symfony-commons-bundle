<?php

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle;

use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\DependencyInjection\Compiler\ViewListenerAliasProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TestBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ViewListenerAliasProviderPass());
    }
}
