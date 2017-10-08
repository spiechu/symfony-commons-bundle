<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetAndDeleteRouteController
{
    /**
     * @Route("/get-request", name="get_request", methods={"GET"})
     *
     * @return Response
     */
    public function getRequestAction(): Response
    {
        return new Response('GET request');
    }

    /**
     * @Route("/delete-request", name="delete_request", methods={"DELETE"})
     *
     * @return Response
     */
    public function deleteRequestAction(): Response
    {
        return new Response('DELETE request');
    }
}
