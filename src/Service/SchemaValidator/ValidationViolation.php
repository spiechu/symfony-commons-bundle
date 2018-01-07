<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

class ValidationViolation
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var null|string
     */
    protected $property;

    /**
     * @param string      $message
     * @param null|string $property
     */
    protected function __construct(string $message, ?string $property = null)
    {
        $this->message = $message;
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s %s', ($property = $this->getProperty()) ? ($property.':') : '', $this->getMessage());
    }

    /**
     * @param string      $message
     * @param null|string $property
     *
     * @return ValidationViolation
     */
    public static function create(string $message, ?string $property = null): self
    {
        return new static($message, $property);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return null|string
     */
    public function getProperty(): ?string
    {
        return $this->property;
    }
}
