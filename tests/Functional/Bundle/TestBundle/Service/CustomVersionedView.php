<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service;

use Spiechu\SymfonyCommonsBundle\Service\VersionedViewInterface;

class CustomVersionedView implements VersionedViewInterface
{
    /**
     * @var string
     */
    protected $version;

    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
