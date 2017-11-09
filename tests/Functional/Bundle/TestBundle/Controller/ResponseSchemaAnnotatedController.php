<?php

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResponseSchemaAnnotatedController extends Controller
{
    /**
     * @Route("/simple-json", name="simple_json")
     *
     * @ResponseSchemaValidator(
     *   json={
     *     200="@TestBundle/Resources/response_schema/200-simple.json",
     *  }
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function simpleJsonAction(Request $request): JsonResponse
    {
        return new JsonResponse([
            'id' => $request->query->get('id'),
        ]);
    }
}
