<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

class ValidationResult
{
    /** @var array */
    protected $errors = [];

    public function addError(string $message, ?string $property = null): self
    {
        $this->errors[] = [$message, $property];

        return $this;
    }

    public function isValid(): bool
    {
        return !count($this->errors);
    }

    /**
     * @return array [ ['message', 'property'] ... ]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
