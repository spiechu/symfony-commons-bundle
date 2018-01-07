<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;

class Utils
{
    /**
     * @param Definition $definition
     * @param int $index
     * @param mixed $value
     *
     * @throws OutOfBoundsException
     */
    public static function addOrReplaceDefinitionArgument(Definition $definition, int $index, $value): void
    {
        if (array_key_exists($index, $definition->getArguments())) {
            $definition->replaceArgument($index, $value);
        } else {
            $definition->setArgument($index, $value);
        }
    }

    /**
     * @param Definition $definition
     */
    public static function clearListenerTags(Definition $definition): void
    {
        $definition->clearTag('kernel.event_subscriber');
        $definition->clearTag('kernel.event_listener');

        $definition->setPublic(false);
    }
}
