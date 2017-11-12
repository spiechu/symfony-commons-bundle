<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

class XmlSchemaValidator implements SchemaValidatorInterface
{
    /**
     * @var string
     */
    protected $schemaLocation;

    public function __construct(string $schemaLocation)
    {
        $this->schemaLocation = $schemaLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(string $xmlString): ValidationResult
    {
        $validationResult = new ValidationResult();
        $useInternalErrors = libxml_use_internal_errors();

        libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = true;
            $doc->formatOutput = true;
            $doc->recover = true;

            $doc->loadXML($xmlString);

            if ($doc->schemaValidateSource(file_get_contents($this->schemaLocation))) {
                return $validationResult;
            }

            foreach (libxml_get_errors() as $error) {
                $validationResult->addViolation(ValidationViolation::create($error->message, 'line '.$error->line));
            }

            return $validationResult;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($useInternalErrors);
        }
    }
}
