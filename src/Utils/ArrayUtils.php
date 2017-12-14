<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Utils;

class ArrayUtils
{
    /**
     * @param null|array $array
     *
     * @return array
     */
    public static function flatArrayRecursive(?array $array): array
    {
        if (empty($array)) {
            return [];
        }

        $resultArray = [];

        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $resultArray = array_merge($resultArray, static::flatArrayRecursive($value));
            } else {
                $resultArray[] = $value;
            }
        }

        return $resultArray;
    }
}
