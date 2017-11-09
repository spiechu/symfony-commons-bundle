<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\ApiFeature;

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
        if ($since !== null && !is_numeric($since)) {
            throw new \InvalidArgumentException('Since parameter is not numeric');
        }
        if ($until !== null && !is_numeric($until)) {
            throw new \InvalidArgumentException('Since parameter is not numeric');
        }

        if ($since === null && $until === null) {
            throw new \InvalidArgumentException('No version constraints provided');
        }

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
        $sinceVersionMatch = $this->since === null || version_compare($version, $this->since, '>=');
        $untilVersionMatch = $this->until === null || version_compare($version, $this->until, '<=');

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
