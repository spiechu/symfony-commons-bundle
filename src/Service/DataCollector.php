<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class DataCollector implements DataCollectorInterface, EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        var_dump('s');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'spiechu_symfony_commons.data_collector';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [];
    }
}
