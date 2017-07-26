<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck;

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
     * @var string[]
     */
    protected $schemaViolations;

    /**
     * @var bool
     */
    protected $wasChecked;

    public function __construct(string $format, string $content, string $responseSchemaLocation)
    {
        $this->format = $format;
        $this->content = $content;
        $this->responseSchemaLocation = $responseSchemaLocation;

        $this->schemaViolations = [];
        $this->wasChecked = false;
    }

    public function addSchemaViolation(string $violation): self
    {
        $this->schemaViolations[] = $violation;

        return $this;
    }

    public function getSchemaViolations(): array
    {
        return $this->schemaViolations;
    }

    public function markChecked(): self
    {
        $this->wasChecked = true;

        return $this;
    }

    public function wasChecked(): bool
    {
        return $this->wasChecked;
    }


}