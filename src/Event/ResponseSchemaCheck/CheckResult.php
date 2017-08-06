<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;

use Spiechu\SymfonyCommonsBundle\Service\ValidationResult;
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

    public function __construct(string $format, string $content, ValidationResult $validationResult)
    {
        $this->format = $format;
        $this->content = $content;
        $this->validationResult = $validationResult;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }

    public function isValid(): bool
    {
        return $this->validationResult->isValid();
    }
}
