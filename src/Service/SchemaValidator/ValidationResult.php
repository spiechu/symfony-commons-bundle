<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

class ValidationResult
{
    /**
     * @var ValidationViolation[]
     */
    protected $errors = [];

    /**
     * @param ValidationViolation $validationViolation
     *
     * @return ValidationResult
     */
    public function addViolation(ValidationViolation $validationViolation): self
    {
        $this->errors[] = $validationViolation;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return !\count($this->errors);
    }

    /**
     * @return ValidationViolation[]
     */
    public function getViolations(): array
    {
        return $this->errors;
    }
}
