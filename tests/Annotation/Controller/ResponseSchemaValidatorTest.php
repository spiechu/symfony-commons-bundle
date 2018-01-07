<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Annotation\Controller;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;

class ResponseSchemaValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /empty schemas/i
     */
    public function testEmptyDataIsNotAcceptable()
    {
        new ResponseSchemaValidator([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /123 is not a string/i
     */
    public function testWrongFormat()
    {
        new ResponseSchemaValidator([
            123 => [],
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /im wrong schema is not an array/i
     */
    public function testWrongSchemas()
    {
        new ResponseSchemaValidator([
            'json' => 'im wrong schema',
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /wrong code is not an integer/i
     */
    public function testWrongResponseSchemaCodes()
    {
        new ResponseSchemaValidator([
            'json' => [
                'wrong code' => 'schema',
            ],
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /12345 is not a string/i
     */
    public function testWrongResponseSchemas()
    {
        new ResponseSchemaValidator([
            'json' => [
                200 => 12345,
            ],
        ]);
    }
}
