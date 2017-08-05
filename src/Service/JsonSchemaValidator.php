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

    public function __construct(\stdClass $schema, Validator $validator)
    {
        $this->schema = $schema;
        $this->validator = $validator;
    }

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

        $this->mapErrors($this->validator, $validationResult);

        return $validationResult;
    }

    protected function mapErrors(Validator $validator, ValidationResult $validationResult): void
    {
        foreach ($validator->getErrors() as $error) {
            $validationResult->addViolation(ValidationViolation::create($error['message'], $error['property']));
        }
    }
}
