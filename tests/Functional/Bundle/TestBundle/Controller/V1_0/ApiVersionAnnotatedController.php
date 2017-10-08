<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @ApiVersion("1.0")
 */
class ApiVersionAnnotatedController extends Controller
{
    /**
     * @Route("/fancy-route")
     */
    public function fancyRouteAction()
    {
        return new Response('response from fancy route');
    }
}