<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ListenerReplacingGetMethodListener
{
    /**
     * @var string
     */
    public $arg1;

    /**
     * @var string[]
     */
    public $arg2;

    /**
     * @var Request
     */
    public $request;

    /**
     * @param string   $arg1
     * @param string[] $arg2
     */
    public function __construct(string $arg1, array $arg2)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    /**
     * @param GetResponseEvent $getResponseEvent
     */
    public function onKernelRequest(GetResponseEvent $getResponseEvent)
    {
        $this->request = $getResponseEvent->getRequest();
    }
}
