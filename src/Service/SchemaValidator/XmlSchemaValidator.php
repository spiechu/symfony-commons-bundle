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
            $document = $this->createDOMDocument($xmlString);

            if ($document->schemaValidateSource(file_get_contents($this->schemaLocation))) {
                return $validationResult;
            }

            /** @var \LibXMLError $error */
            foreach (libxml_get_errors() as $error) {
                $validationResult->addViolation(ValidationViolation::create($error->message, 'line '.$error->line));
            }

            return $validationResult;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($useInternalErrors);
        }
    }

    /**
     * @param string $xmlString
     *
     * @return \DOMDocument
     */
    protected function createDOMDocument(string $xmlString): \DOMDocument
    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = true;
        $document->formatOutput = true;
        $document->recover = true;

        $document->loadXML($xmlString);

        return $document;
    }
}
