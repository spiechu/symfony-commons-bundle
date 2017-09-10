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
     * @var array [string format => [int responseCode => string pathToSchemaFile]]
     */
    protected $schemas = [];

    /**
     * @param array $data [string format => [int responseCode => string pathToSchemaFile]]
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Empty schemas provided');
        }

        foreach ($data as $format => $schemas) {
            if (!is_string($format)) {
                throw new \InvalidArgumentException($format.' is not a string');
            }

            if (!is_array($schemas)) {
                throw new \InvalidArgumentException($schemas.' is not an array');
            }

            $this->loadFormatSchemas($format, $schemas);
        }
    }

    public function getSchemas(): array
    {
        return $this->schemas;
    }

    protected function loadFormatSchemas(string $format, array $schemas): void
    {
        $format = strtolower($format);
        $this->schemas[$format] = [];

        foreach ($schemas as $responseCode => $schema) {
            if (!is_int($responseCode)) {
                throw new \InvalidArgumentException($responseCode.' is not an integer');
            }

            if (!is_string($schema)) {
                throw new \InvalidArgumentException($schema.' is not a string');
            }

            $this->schemas[$format][$responseCode] = $schema;
        }
    }
}
