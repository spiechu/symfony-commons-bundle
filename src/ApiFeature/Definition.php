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
     * @throws \InvalidArgumentException
     *
     * @return Definition
     */
    public static function create(string $name, ?string $since, ?string $until): Definition
    {
        StringUtils::assertNotEmpty($name, 'Empty feature name');
        StringUtils::assertNumericOrNull($since, 'Since parameter is not numeric');
        StringUtils::assertNumericOrNull($until, 'Until parameter is not numeric');
        StringUtils::assertAtLeastOneArgumentNotNull('No version constraints provided', $since, $until);

        static::assertUntilVersionAtLeastSameAsSince($since, $until);

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
     * @throws \InvalidArgumentException When $until parameter is lower than $since parameter
     */
    protected static function assertUntilVersionAtLeastSameAsSince(?string $since, ?string $until): void
    {
        if ($since === null || $until === null) {
            return;
        }

        if (version_compare($until, $since, '<')) {
            throw new \InvalidArgumentException('Until parameter is lower than since parameter');
        }
    }
}
