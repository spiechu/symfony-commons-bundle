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

    /**
     * @param null|string $string
     * @param string      $errorMessage
     *
     * @throws \InvalidArgumentException
     */
    public static function assertNumericOrNull(?string $string, string $errorMessage = 'Parameter is not numeric'): void
    {
        if (null !== $string && !is_numeric($string)) {
            throw new \InvalidArgumentException($errorMessage);
        }
    }

    /**
     * @param string          $errorMessage
     * @param null[]|string[] ...$arguments
     *
     * @throws \InvalidArgumentException
     */
    public static function assertAtLeastOneArgumentNotNull(string $errorMessage, ?string ...$arguments): void
    {
        foreach ($arguments as $argument) {
            if (null !== $argument) {
                return;
            }
        }

        throw new \InvalidArgumentException($errorMessage);
    }
}
