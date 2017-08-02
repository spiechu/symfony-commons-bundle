<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Utils;

class StringUtils
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function assertNotEmpty(string $string): void
    {
        if ('' === $string) {
            throw new \InvalidArgumentException('Empty string provided');
        }
    }
}
