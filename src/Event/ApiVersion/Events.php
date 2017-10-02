<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ApiVersion;

final class Events
{
    /**
     * @Event("Spiechu\SymfonyCommonsBundle\Event\ApiVersion\ApiVersionSetEvent")
     */
    public const API_VERSION_SET = 'spiechu_symfony_commons.event.api_version.api_version_set';
}
