<?php

declare(strict_types=1);

namespace Spiechu\SymfonyCommonsBundle\Test\Annotation\Controller;

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;

class ApiVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getAcceptableVersionFormats
     *
     * @param mixed $acceptableVersionFormat
     */
    public function testAcceptableFormats($acceptableVersionFormat)
    {
        self::assertInstanceOf(ApiVersion::class, new ApiVersion([
            'value' => $acceptableVersionFormat,
        ]));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /no api version/i
     */
    public function testEmptyDataIsNotAcceptable()
    {
        new ApiVersion([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /api version must have.{1,}format/i
     *
     * @dataProvider getNotAcceptableVersionFormats
     *
     * @param mixed $notAcceptableVersionFormat
     */
    public function testWrongVersionFormatIsNotAcceptable($notAcceptableVersionFormat)
    {
        new ApiVersion([
            'value' => $notAcceptableVersionFormat,
        ]);
    }

    /**
     * @return array
     */
    public function getAcceptableVersionFormats(): array
    {
        return [
            [0],
            [1],
            [1.1],
            ['0'],
            ['1'],
            ['2'],
            ['0.1'],
            ['0.2'],
            ['1.0'],
            ['1.1'],
            ['1.2'],
            ['100.99'],
        ];
    }

    /**
     * @return array
     */
    public function getNotAcceptableVersionFormats(): array
    {
        return [
            [''],
            ['wrong format'],
            ['.5'],
            ['1,4'],
        ];
    }
}
