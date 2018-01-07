<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Utils;

class StringUtils
{
    /**
     * @param string $string
     * @param string $errorMessage
     *
     * @throws \InvalidArgumentException
     */
    public static function assertNotEmpty(string $string, string $errorMessage = 'Empty string provided'): void
    {
        if ('' === $string) {
            throw new \InvalidArgumentException($errorMessage);
        }
    }
}
