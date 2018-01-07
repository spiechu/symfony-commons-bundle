<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\EventListener;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;

class ListenerReplacingFailedSchemaCheckListener
{
    /**
     * @var CheckResult
     */
    public $checkResult;

    /**
     * @param CheckResult $checkResult
     */
    public function onCheckResult(CheckResult $checkResult): void
    {
        $this->checkResult = $checkResult;
    }
}
