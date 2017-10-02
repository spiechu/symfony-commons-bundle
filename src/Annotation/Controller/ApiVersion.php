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
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \InvalidArgumentException('No API version provided');
        }

        if (!preg_match('/^[0-9]{1}\.[0-9]{1}$/', $values['value'])) {
            throw new \InvalidArgumentException('API version must have X.X format');
        }

        $this->apiVersion = (string) $values['value'];
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }
}
