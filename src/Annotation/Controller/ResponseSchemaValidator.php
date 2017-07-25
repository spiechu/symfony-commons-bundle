<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Annotation\Controller;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class ResponseSchemaValidator
{
    /**
     * @var array [int responseCode => string pathToSchemaFile]
     */
    protected $xmlSchemas = [];

    /**
     * @var array [int responseCode => string pathToSchemaFile]
     */
    protected $jsonSchemas = [];

    /**
     * @param array $data
     *
     * @throws \InvalidArgumentException When parameters different than 'xml' or 'json' provided
     */
    public function __construct(array $data)
    {
        if (!empty($data['xml'])) {
            $this->xmlSchemas = (array)$data['xml'];
        }

        if (!empty($data['json'])) {
            $this->jsonSchemas = (array)$data['json'];
        }

        unset($data['xml'], $data['json']);

        if (!empty($data)) {
            throw new \InvalidArgumentException('Only "xml" and "json" parameters are supported');
        }
    }

    public function getXmlSchemas(): array
    {
        return $this->xmlSchemas;
    }

    public function getJsonSchemas(): array
    {
        return $this->jsonSchemas;
    }
}
