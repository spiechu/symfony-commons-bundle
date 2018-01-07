<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Service\SchemaValidator;

use JsonSchema\Validator;

class JsonSchemaValidator implements SchemaValidatorInterface
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
     * {@inheritdoc}
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
            $validationResult->addViolation(ValidationViolation::create(sprintf('Validator check exception: "%s"', $e->getMessage())));
        }

        $this->mapErrorsToResultViolations($validationResult);

        return $validationResult;
    }

    /**
     * @param ValidationResult $validationResult
     */
    protected function mapErrorsToResultViolations(ValidationResult $validationResult): void
    {
        foreach ($this->validator->getErrors() as $error) {
            $validationResult->addViolation(ValidationViolation::create($error['message'], $error['property']));
        }
    }
}
