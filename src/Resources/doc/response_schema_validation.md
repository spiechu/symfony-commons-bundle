When exposing API to external services, sticking to agreed schema is crucial.
Decent API consumers will most probably validate incoming data.
How will your API look like when you won't give data in proper form as promised?

If your endpoint supports multiple formats divided by returned status codes you can setup it all in one single `@ResponseSchemaValidator` annotation like:

```php
    /**
     * @Route("/", name="my_route")
     *
     * @ResponseSchemaValidator(
     *  json={
     *   200="@AppBundle/Resources/response_schema/my_route_200.json",
     *   500="@AppBundle/Resources/response_schema/my_route_500.json"
     *  },
     *  xml={
     *   200="@AppBundle/Resources/response_schema/my_route_200.xsd",
     *   500="@AppBundle/Resources/response_schema/my_route_500.xsd"
     *  }
     * )
     */
    public function indexAction(): Response
```
