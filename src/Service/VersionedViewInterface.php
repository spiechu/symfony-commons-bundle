<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

interface VersionedViewInterface
{
    /**
     * @param string $version
     */
    public function setVersion(string $version): void;
}
