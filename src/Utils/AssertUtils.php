<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Utils;

class AssertUtils
{
    /**
     * @param iterable|string[] $elements
     *
     * @return string[]
     */
    public static function getNonStrings(iterable $elements): array
    {
        $nonStrings = [];

        foreach ($elements as $string) {
            if (!\is_string($string)) {
                $nonStrings[] = $string;
            }
        }

        return $nonStrings;
    }
}
