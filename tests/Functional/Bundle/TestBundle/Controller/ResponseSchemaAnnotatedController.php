<?php

namespace Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Controller;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;
use Spiechu\SymfonyCommonsBundle\Test\Functional\Bundle\TestBundle\Annotation\OverwrittenResponseSchemaValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route("/not-valid-json", name="not_valid_json")
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
    public function notValidJsonAction(Request $request): JsonResponse
    {
        return new JsonResponse([
            'not-id' => $request->query->get('id'),
        ]);
    }

    /**
     * @Route("/multiple-schemas-endpoint", name="multiple_schemas_endpoint")
     *
     * @ResponseSchemaValidator(
     *   json={
     *     200="@TestBundle/Resources/response_schema/200-simple.json",
     *     400="@TestBundle/Resources/response_schema/400-simple.json",
     *  },
     *  xml={
     *     200="@TestBundle/Resources/response_schema/200-simple.xsd",
     *     400="@TestBundle/Resources/response_schema/400-simple.xsd",
     *  }
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function multipleSchemasEndpointAction(Request $request): JsonResponse
    {
        return new JsonResponse([
            'error' => $request->query->get('error'),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/simple-xml", name="simple_xml")
     *
     * @OverwrittenResponseSchemaValidator(
     *   xml={
     *     200="@TestBundle/Resources/response_schema/200-simple.xsd",
     *  }
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function simpleXmlAction(Request $request): Response
    {
        return new Response(
            <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<testObjects>
    <testObject>
        <string-property>im test string property</string-property>
        <int-property>{$request->query->get('id')}</int-property>
    </testObject>
</testObjects>
EOT
            ,
            200,
            [
                'Content-Type' => 'text/xml',
            ]
        );
    }

    /**
     * @Route("/not-valid-simple-xml", name="not_valid_simple_xml")
     *
     * @ResponseSchemaValidator(
     *   xml={
     *     200="@TestBundle/Resources/response_schema/200-simple.xsd",
     *  }
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function notValidSimpleXmlAction(Request $request): Response
    {
        return new Response(
            <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<testObjects>
    <testObject>
        <unexpected-property>im unexpected property</unexpected-property>
        <int-property>{$request->query->get('id')}</int-property>
    </testObject>
</testObjects>
EOT
            ,
            200,
            [
                'Content-Type' => 'text/xml',
            ]
        );
    }
}
