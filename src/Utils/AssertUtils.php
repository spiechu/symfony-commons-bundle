<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Utils;

class AssertUtils
{
    /**
     * @param iterable|string[] $elements
     *
     * @return bool
     */
    public static function hasNonStrings(iterable $elements): bool
    {
        foreach ($elements as $string) {
            if (!\is_string($string)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isNotEmpty(string $string): bool
    {
        return '' !== $string;
    }

    /**
     * @param null|string $string
     *
     * @return bool
     */
    public static function isNumericOrNull(?string $string): bool
    {
        return !(null !== $string && !is_numeric($string));
    }

    /**
     * @param null[]|string[] ...$arguments
     *
     * @return bool
     */
    public static function isAtLeastOneArgumentNotNull(?string ...$arguments): bool
    {
        foreach ($arguments as $argument) {
            if (null !== $argument) {
                return true;
            }
        }

        return false;
    }
}
