<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\CheckResult;
use Spiechu\SymfonyCommonsBundle\Event\ResponseSchemaCheck\Events;
use Spiechu\SymfonyCommonsBundle\EventListener\RequestSchemaValidatorListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseDataCollector;

class DataCollector extends BaseDataCollector implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['known_response_schemas'] = $request->attributes->has(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
            ? $request->attributes->get(RequestSchemaValidatorListener::ATTRIBUTE_RESPONSE_SCHEMAS)
            : null;
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
        return [
            Events::CHECK_RESULT => 'onCheckResult',
        ];
    }

    public function onCheckResult(CheckResult $checkResult)
    {
        $this->data['validation_result'] = $checkResult->getValidationResult();
    }
}
