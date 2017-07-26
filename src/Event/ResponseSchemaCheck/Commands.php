<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;


final class Commands
{
    private const CHECK_SCHEMA_PATTERN = 'spiechu_symfony_commons.event.response_schema_check.check_schema_%s';

    public static function getCheckSchemaEventNameFor(string $format): string
    {
        return sprintf(self::CHECK_SCHEMA_PATTERN, $format);
    }
}
