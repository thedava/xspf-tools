<?php

namespace XspfTest\Utils;

use PHPUnit\Framework\TestCase;
use Xspf\Utils\BytesFormatter;

class BytesFormatterTest extends TestCase
{
    /**
     * @return array[]
     */
    public function bytesDataProvider()
    {
        return [
            // Regular
            [pow(1024, 0), '1 B'],
            [pow(1024, 0) + 1, '2 B'],
            [pow(1024, 1), '1,00 KB'],
            [pow(1024, 1) + 8, '1,01 KB'],
            [pow(1024, 2), '1,00 MB'],
            [pow(1024, 3), '1,00 GB'],
            [pow(1024, 4), '1,00 TB'],
            [pow(1024, 5), '1,00 PB'],
            //
            // PB is the current maximum so further increase will not change the symbol
            [pow(1024, 6), '1.024,00 PB'],
        ];
    }

    /**
     * @dataProvider bytesDataProvider
     *
     * @param int    $bytes
     * @param string $expectedResult
     */
    public function testFormatBytes(int $bytes, string $expectedResult)
    {
        $this->assertEquals($expectedResult, BytesFormatter::formatBytes($bytes));
    }
}
