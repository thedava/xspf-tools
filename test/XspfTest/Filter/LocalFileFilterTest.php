<?php

namespace XspfTest\Filter;

use PHPUnit\Framework\TestCase;
use Xspf\Filter\LocalFileFilter;

class LocalFileFilterTest extends TestCase
{
    public function localFileDataProvider()
    {
        return [
            // Windows
            ['file:///C:/Video/video.mp4', '\\', 'C:\Video\video.mp4'],
            ['file:///C:/Video/spaces%20video.mp4', '\\', 'C:\Video\spaces video.mp4'],
            ['file:///C:/Video/%28strange%29%20symbols%20video.mp4', '\\', 'C:\Video\(strange) symbols video.mp4'],

            // Linux
            ['file:///home/user/video/video.mp4', '/', '/home/user/video/video.mp4'],
            ['file:///home/user/video/spaces%20video.mp4', '/', '/home/user/video/spaces video.mp4'],
            ['file:///home/user/video/%28strange%29%20symbols%20video.mp4', '/', '/home/user/video/(strange) symbols video.mp4'],
        ];
    }

    /**
     * @dataProvider localFileDataProvider
     *
     * @param string $fileUrl
     * @param string $directorySeparator
     * @param string $expectedLocalFile
     */
    public function testFilter($fileUrl, $directorySeparator, $expectedLocalFile)
    {
        $this->assertEquals($expectedLocalFile, LocalFileFilter::filter($fileUrl, $directorySeparator));
    }
}
