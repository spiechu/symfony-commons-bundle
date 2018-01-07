<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
class ApiVersion
{
    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @param array $values
     *
     * @throws \InvalidArgumentException When no API version provided or wrong format
     */
    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \InvalidArgumentException('No API version provided');
        }

        $stringifiedValue = (string) $values['value'];

        if (!preg_match('/^\d{1,}(?:\.\d{1,}){0,1}$/', $stringifiedValue)) {
            throw new \InvalidArgumentException('API version must have X or X.X format');
        }

        $this->apiVersion = $stringifiedValue;
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }
}
