<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;

class FailedSchemaCheckListener
{
    /**
     * @param CheckResult $checkResult
     *
     * @throws \RuntimeException
     */
    public function onCheckResult(CheckResult $checkResult): void
    {
        if ($checkResult->isValid()) {
            return;
        }

        throw new \RuntimeException(sprintf(
            '"%s" Schema violations: "%s"',
            $checkResult->getFormat(),
            implode(', ', $checkResult->getValidationResult()->getViolations())
        ));
    }
}
