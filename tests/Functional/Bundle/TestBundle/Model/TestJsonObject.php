<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Model;

use JMS\Serializer\SerializationContext;

class TestJsonObject
{
    /**
     * @var string
     */
    protected $property1;

    /**
     * @var string
     */
    protected $property2;

    /**
     * @param string $property1
     * @param string $property2
     */
    public function __construct(string $property1, string $property2)
    {
        $this->property1 = $property1;
        $this->property2 = $property2;
    }

    /**
     * @return string
     */
    public function getProperty1(): string
    {
        return $this->property1;
    }

    /**
     * @return string
     */
    public function getProperty2(): string
    {
        return $this->property2;
    }

    /**
     * @param SerializationContext $context
     *
     * @return bool
     */
    public function exposeIfVersionSet(SerializationContext $context): bool
    {
        return $context->attributes->containsKey('version');
    }
}
