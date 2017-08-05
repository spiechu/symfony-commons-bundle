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
     * @var string|null
     */
    protected $property;

    public static function create(string $message, ?string $property = null): self
    {
        return new static($message, $property);
    }

    protected function __construct(string $message, ?string $property = null)
    {
        $this->message = $message;
        $this->property = $property;
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

    public function __toString(): string
    {
        return sprintf('%s: %s', $this->property ?: '', $this->message);
    }
}
