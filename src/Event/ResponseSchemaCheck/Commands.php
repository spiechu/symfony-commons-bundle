<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;

use Spiechu\SymfonyCommonsBundle\Utils\StringUtils;

final class Commands
{
    private const CHECK_SCHEMA_PATTERN = 'spiechu_symfony_commons.event.response_schema_check.check_schema_%s';

    public static function getCheckSchemaEventNameFor(string $format): string
    {
        StringUtils::assertNotEmpty($format);

        return sprintf(self::CHECK_SCHEMA_PATTERN, strtolower($format));
    }
}
