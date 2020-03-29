<?php

namespace XspfTest;

use PHPUnit\Framework\TestCase;
use Xspf\Filter\FileUrlFilter;

class FileUrlFilterTest extends TestCase
{
    public function fileNameDataProvider()
    {
        return [
            ['foo', 'foo'],
            ['ñ', '%C3%B1'],
            ['é', '%C3%A9'],
            ['ö', '%C3%B6'],
            ['Ö', '%C3%96'],
            ['ü', '%C3%BC'],
            ['Ü', '%C3%9C'],
            ['ä', '%C3%A4'],
            ['Ä', '%C3%84'],
            ['ß', '%C3%9F'],
        ];
    }

    /**
     * @dataProvider fileNameDataProvider
     *
     * @param string $fileName
     * @param string $expectedResult
     */
    public function testFilter($fileName, $expectedResult)
    {
        $this->assertEquals($expectedResult, preg_replace('#^(file://)#', '', FileUrlFilter::filter($fileName)));
    }
}
