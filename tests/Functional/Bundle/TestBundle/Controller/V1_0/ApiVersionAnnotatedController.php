<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller\V1_0;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Service\CustomVersionedView;
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

    /**
     * @Route("/custom-versioned-view")
     *
     * @return CustomVersionedView
     */
    public function routeWithCustomVersionedView(): CustomVersionedView
    {
        return new CustomVersionedView();
    }
}
