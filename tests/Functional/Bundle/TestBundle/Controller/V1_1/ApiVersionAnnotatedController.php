<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_1;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0\ApiVersionAnnotatedController as BaseApiVersionAnnotatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ApiVersion("1.1")
 */
class ApiVersionAnnotatedController extends BaseApiVersionAnnotatedController
{
    /**
     * @Route("/fancy-route")
     */
    public function fancyRouteAction()
    {
        return new Response('response from fancy route');
    }
}
