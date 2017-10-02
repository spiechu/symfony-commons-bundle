<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;

use Spiechu\SymfonyCommonsBundle\Utils\StringUtils;

final class Events
{
    /**
     * @Event("Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult")
     */
    public const CHECK_RESULT = 'spiechu_symfony_commons.event.response_schema_check.check_result';

    private const CHECK_SCHEMA_EVENT_NAME_PATTERN = 'spiechu_symfony_commons.event.response_schema_check.check_schema_%s';

    /**
     * @param string $format
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function getCheckSchemaEventNameFor(string $format): string
    {
        StringUtils::assertNotEmpty($format);

        return sprintf(self::CHECK_SCHEMA_EVENT_NAME_PATTERN, strtolower($format));
    }
}
