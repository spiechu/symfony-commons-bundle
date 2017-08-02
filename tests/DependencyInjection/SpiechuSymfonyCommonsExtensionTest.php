<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\DependencyInjection;

use Spiechu\SymfonyCommonsBundle\DependencyInjection\SpiechuSymfonyCommonsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SpiechuSymfonyCommonsExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var SpiechuSymfonyCommonsExtension
     */
    protected $extension;

    public function testNoListenersSetUpOnDefaultConfiguration()
    {
        $config = [
            'spiechu_symfony_commons' => [],
        ];

        $this->extension->load($config, $this->container);

        foreach (array_keys($this->container->getDefinitions()) as $id) {
            self::assertNotRegExp('/^spiechu_symfony_commons\..{1,}_listener$/i', $id);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.bundles', array('JMSSerializerBundle' => true));
        $this->container->setParameter('kernel.debug', false);
        $this->extension = new SpiechuSymfonyCommonsExtension();
    }
}
