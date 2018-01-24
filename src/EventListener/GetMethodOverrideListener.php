<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\EventListener;

use Spiechu\SymfonyCommonsBundle\Utils\AssertUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class GetMethodOverrideListener
{
    public const ATTRIBUTE_REQUEST_GET_METHOD_OVERRIDE = 'spiechu_symfony_commons.event_listener.get_method_override';

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

        \assert(
            !AssertUtils::hasNonStrings($methodsToOverride),
            '$methodsToOverride contain non string elements'
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

        if (!$request->isMethod(Request::METHOD_GET)) {
            return;
        }

        $this->overrideRequestMethod($request);
    }

    /**
     * @param Request $request
     */
    protected function overrideRequestMethod(Request $request): void
    {
        if (!$request->query->has($this->queryParamName)) {
            return;
        }

        $normalizedQueryParam = strtoupper($request->query->get($this->queryParamName));

        if (\in_array($normalizedQueryParam, $this->methodsToOverride, true)) {
            $request->setMethod($normalizedQueryParam);

            $request->attributes->set(static::ATTRIBUTE_REQUEST_GET_METHOD_OVERRIDE, $normalizedQueryParam);
        }
    }
}
