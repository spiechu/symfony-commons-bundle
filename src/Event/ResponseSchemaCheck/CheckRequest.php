<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;

use Spiechu\SymfonyCommonsBundle\Service\ValidationResult;
use Symfony\Component\EventDispatcher\Event;

class CheckRequest extends Event
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
     * @var string
     */
    protected $responseSchemaLocation;

    /**
     * @var null|ValidationResult
     */
    protected $validationResult;

    public function __construct(string $format, string $content, string $responseSchemaLocation)
    {
        $this->format = $format;
        $this->content = $content;
        $this->responseSchemaLocation = $responseSchemaLocation;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getResponseSchemaLocation(): string
    {
        return $this->responseSchemaLocation;
    }

    public function setValidationResult(ValidationResult $validationResult): self
    {
        $this->validationResult = $validationResult;

        return $this;
    }

    public function getValidationResult(): ?ValidationResult
    {
        return $this->validationResult;
    }
}
