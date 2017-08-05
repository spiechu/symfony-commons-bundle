<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

class ValidationResult
{
    /** @var ValidationViolation[] */
    protected $errors = [];

    public function addViolation(ValidationViolation $validationViolation): self
    {
        $this->errors[] = $validationViolation;

        return $this;
    }

    public function isValid(): bool
    {
        return !count($this->errors);
    }

    /**
     * @return ValidationViolation[]
     */
    public function getViolations(): array
    {
        return $this->errors;
    }
}
