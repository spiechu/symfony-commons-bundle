<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\ApiFeature;

use Spiechu\SymfonyCommonsBundle\Utils\StringUtils;

class Definition implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $since;

    /**
     * @var null|string
     */
    protected $until;

    /**
     * @param string      $name
     * @param null|string $since
     * @param null|string $until
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name, ?string $since, ?string $until)
    {
        StringUtils::assertNotEmpty($name, 'Empty feature name');
        StringUtils::assertNumericOrNull($since, 'Since parameter is not numeric');
        StringUtils::assertNumericOrNull($until, 'Until parameter is not numeric');
        StringUtils::assertAtLeastOneArgumentNotNull('No version constraints provided', $since, $until);

        $this->name = $name;
        $this->since = $since;
        $this->until = $until;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getSince(): ?string
    {
        return $this->since;
    }

    /**
     * @return null|string
     */
    public function getUntil(): ?string
    {
        return $this->until;
    }

    /**
     * @param string $version
     *
     * @return bool
     */
    public function isAvailableForVersion(string $version): bool
    {
        $sinceVersionMatch = null === $this->since || version_compare($version, $this->since, '>=');
        $untilVersionMatch = null === $this->until || version_compare($version, $this->until, '<=');

        return $sinceVersionMatch && $untilVersionMatch;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'since' => $this->since,
            'until' => $this->until,
        ];
    }
}
