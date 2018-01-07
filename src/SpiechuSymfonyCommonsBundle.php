<?php

namespace Spiechu\SymfonyCommonsBundle;

use Spiechu\SymfonyCommonsBundle\DependencyInjection\Compiler\OverridingListenersSetupPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SpiechuSymfonyCommonsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverridingListenersSetupPass());
    }
}
