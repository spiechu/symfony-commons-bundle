<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\ApiFeature;

use Spiechu\SymfonyCommonsBundle\Utils\AssertUtils;

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
     */
    protected function __construct(string $name, ?string $since, ?string $until)
    {
        $this->name = $name;
        $this->since = $since;
        $this->until = $until;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @param string      $name
     * @param null|string $since
     * @param null|string $until
     *
     * @return static
     */
    public static function create(string $name, ?string $since, ?string $until)
    {
        \assert(
            AssertUtils::isNotEmpty($name),
            'Empty feature name'
        );
        \assert(
            AssertUtils::isNumericOrNull($since),
            'Since parameter is not numeric'
        );
        \assert(
            AssertUtils::isNumericOrNull($until),
            'Until parameter is not numeric'
        );
        \assert(
            AssertUtils::isAtLeastOneArgumentNotNull($since, $until),
            'No version constraints provided'
        );
        \assert(
            static::isUntilVersionAtLeastSameAsSince($since, $until),
            'Until parameter is lower than since parameter'
        );

        return new static($name, $since, $until);
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

    /**
     * @param null|string $since
     * @param null|string $until
     *
     * @return bool
     */
    protected static function isUntilVersionAtLeastSameAsSince(?string $since, ?string $until): bool
    {
        if (null === $since || null === $until) {
            return true;
        }

        if (version_compare($until, $since, '<')) {
            return false;
        }

        return true;
    }
}
