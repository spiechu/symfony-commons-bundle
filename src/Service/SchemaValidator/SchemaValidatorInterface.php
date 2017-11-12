<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

interface SchemaValidatorInterface
{
    /**
     * @param string $string
     *
     * @return ValidationResult
     */
    public function validate(string $string): ValidationResult;
}
