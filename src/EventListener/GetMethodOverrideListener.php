<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Utils\AssertUtils;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class GetMethodOverrideListener
{
    /**
     * @var string
     */
    protected $queryParamName;

    /**
     * @var string[]
     */
    protected $methodsToOverride;

    /**
     * @param string   $queryParamName
     * @param string[] $methodsToOverride
     */
    public function __construct(string $queryParamName, array $methodsToOverride)
    {
        $this->queryParamName = $queryParamName;

        assert(
            empty(AssertUtils::getNonStrings($methodsToOverride)),
            'contains non string elements'
        );

        $this->methodsToOverride = $methodsToOverride;
    }

    /**
     * @param GetResponseEvent $getResponseEvent
     */
    public function onKernelRequest(GetResponseEvent $getResponseEvent): void
    {
        if (!$getResponseEvent->isMasterRequest()) {
            return;
        }

        $request = $getResponseEvent->getRequest();

        if (!$request->isMethod('GET')) {
            return;
        }

        if (!$request->query->has($this->queryParamName)) {
            return;
        }

        $normalizedQueryParam = strtoupper($request->query->get($this->queryParamName));

        if (in_array($normalizedQueryParam, $this->methodsToOverride, true)) {
            $request->setMethod($normalizedQueryParam);
        }
    }
}
