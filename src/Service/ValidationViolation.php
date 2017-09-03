<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

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

    protected function __construct(string $message, ?string $property = null)
    {
        $this->message = $message;
        $this->property = $property;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->property ? ($this->property.': ') : '', $this->message);
    }

    public static function create(string $message, ?string $property = null): self
    {
        return new static($message, $property);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }
}
