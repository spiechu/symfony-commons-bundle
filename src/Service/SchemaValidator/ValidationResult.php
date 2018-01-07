<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

class ValidationResult implements \IteratorAggregate, \Countable
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
        return empty($this->errors);
    }

    /**
     * @return ValidationViolation[]
     */
    public function getViolations(): array
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     *
     * @return \ArrayIterator|ValidationViolation[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->errors);
    }

    /**
     * {@inheritdoc.
     */
    public function count(): int
    {
        return \count($this->errors);
    }
}
