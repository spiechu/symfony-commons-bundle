When exposing API to external services, sticking to agreed schema is crucial.
Decent API consumers will most probably validate incoming data.

How will your API look like when you won't give data in proper form as promised?

Without any additional effort you can validate JSON and XML data according to schemas.

At most [JSON Schema draft 4](http://json-schema.org/specification-links.html#draft-4) is supported by underlying [justinrainbow/json-schema](https://github.com/justinrainbow/json-schema) library.
Simplest JSON schema below:

```json
{
  "$schema": "http://json-schema.org/draft-04/schema#",
  "type": "object",
  "required": [
    "id"
  ],
  "properties": {
    "id": {
      "type": "string"
    }
  },
  "additionalProperties": false
}
```

XML schema validation is provided by PHP XML extension.
Simplest XSD below:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" version="1.0">

    <xs:element name="testObjects" type="testObjects"/>

    <xs:complexType name="testObjects">
        <xs:sequence>
            <xs:element maxOccurs="unbounded" minOccurs="1" name="testObject" type="object"/>
        </xs:sequence>
    </xs:complexType>

    <xs:complexType name="object">
        <xs:sequence>
            <xs:element minOccurs="1" name="string-property" type="xs:string"/>
            <xs:element minOccurs="1" name="int-property" type="xs:int"/>
        </xs:sequence>
    </xs:complexType>

</xs:schema>
```

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
