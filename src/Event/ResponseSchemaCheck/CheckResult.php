<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;

use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\ValidationResult;
use Symfony\Component\EventDispatcher\Event;

class CheckResult extends Event
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var ValidationResult
     */
    protected $validationResult;

    /**
     * @param string           $format
     * @param string           $content
     * @param ValidationResult $validationResult
     */
    public function __construct(string $format, string $content, ValidationResult $validationResult)
    {
        $this->format = $format;
        $this->content = $content;
        $this->validationResult = $validationResult;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return ValidationResult
     */
    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->validationResult->isValid();
    }
}
