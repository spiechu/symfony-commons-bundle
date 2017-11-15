<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ApiVersion("1.0")
 */
class ApiVersionAnnotatedController extends Controller
{
    /**
     * @Route("/fancy-route")
     *
     * @return Response
     */
    public function fancyRouteAction(): Response
    {
        return new Response('response from fancy route');
    }
}
