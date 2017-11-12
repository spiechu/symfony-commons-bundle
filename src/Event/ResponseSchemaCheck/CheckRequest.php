<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;

use Spiechu\SymfonyCommonsBundle\Service\SchemaValidator\ValidationResult;
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

    /**
     * @param string $format
     * @param string $content
     * @param string $responseSchemaLocation
     */
    public function __construct(string $format, string $content, string $responseSchemaLocation)
    {
        $this->format = $format;
        $this->content = $content;
        $this->responseSchemaLocation = $responseSchemaLocation;
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
     * @return string
     */
    public function getResponseSchemaLocation(): string
    {
        return $this->responseSchemaLocation;
    }

    /**
     * @param ValidationResult $validationResult
     *
     * @return CheckRequest
     */
    public function setValidationResult(ValidationResult $validationResult): self
    {
        $this->validationResult = $validationResult;

        return $this;
    }

    /**
     * @return null|ValidationResult
     */
    public function getValidationResult(): ?ValidationResult
    {
        return $this->validationResult;
    }
}
