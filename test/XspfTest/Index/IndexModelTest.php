<?php

namespace XspfTest\Index;

use Xspf\Index\IndexModel;

class IndexModelTest extends \PHPUnit_Framework_TestCase
{
    public function compressionDataProvider()
    {
        return [
            ['foo.xd', false],
            ['foo.xdc', true],
            ['index.xd', false],
            ['index.xdc', true],
            ['a.xd', false],
            ['a.xdc', true],

            ['index.txt', true],
        ];
    }

    /**
     * @dataProvider compressionDataProvider
     *
     * @param string $fileName
     * @param bool   $expectedResult
     */
    public function testShouldUseCompression($fileName, $expectedResult)
    {
        $this->assertEquals((new IndexModel($fileName))->shouldUseCompression(), $expectedResult);
    }
}
