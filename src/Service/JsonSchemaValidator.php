<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service;

use JsonSchema\Validator;

class JsonSchemaValidator
{
    /**
     * @var \stdClass
     */
    protected $schema;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @param \stdClass $schema
     * @param Validator $validator
     */
    public function __construct(\stdClass $schema, Validator $validator)
    {
        $this->schema = $schema;
        $this->validator = $validator;
    }

    /**
     * @param string $jsonString
     *
     * @return ValidationResult
     */
    public function validate(string $jsonString): ValidationResult
    {
        $this->validator->reset();

        $validationResult = new ValidationResult();
        $decodedJson = json_decode($jsonString);

        if (null === $decodedJson) {
            $validationResult->addViolation(ValidationViolation::create('Not a JSON or invalid format'));

            return $validationResult;
        }

        try {
            $this->validator->check($decodedJson, $this->schema);
        } catch (\Exception $e) {
            $validationResult->addViolation(ValidationViolation::create($e->getMessage()));
        }

        $this->mapErrorsToResultViolations($this->validator, $validationResult);

        return $validationResult;
    }

    /**
     * @param Validator        $validator
     * @param ValidationResult $validationResult
     */
    protected function mapErrorsToResultViolations(Validator $validator, ValidationResult $validationResult): void
    {
        foreach ($validator->getErrors() as $error) {
            $validationResult->addViolation(ValidationViolation::create($error['message'], $error['property']));
        }
    }
}
