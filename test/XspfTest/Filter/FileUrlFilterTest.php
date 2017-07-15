<?php

namespace XspfTest\Filter;

use Xspf\Filter\FileUrlFilter;

class FileUrlFilterTest extends \PHPUnit_Framework_TestCase
{
    public function fileUrlDataProvider()
    {
        return [
            // Windows
            ['C:\Video\video.mp4', '\\', 'file:///C:/Video/video.mp4'],
            ['C:\Video\spaces video.mp4', '\\', 'file:///C:/Video/spaces%20video.mp4'],
            ['C:\Video\(strange) symbols video.mp4', '\\', 'file:///C:/Video/%28strange%29%20symbols%20video.mp4'],

            // Windows (Cygwin)
            ['/cygdrive/c/Video/video.mp4', '/', 'file:///C:/Video/video.mp4'],
            ['/cygdrive/c/Video/spaces video.mp4', '/', 'file:///C:/Video/spaces%20video.mp4'],
            ['/cygdrive/c/Video/(strange) symbols video.mp4', '/', 'file:///C:/Video/%28strange%29%20symbols%20video.mp4'],

            // Linux
            ['/home/user/video/video.mp4', '/', 'file:///home/user/video/video.mp4'],
            ['/home/user/video/spaces video.mp4', '/', 'file:///home/user/video/spaces%20video.mp4'],
            ['/home/user/video/(strange) symbols video.mp4', '/', 'file:///home/user/video/%28strange%29%20symbols%20video.mp4'],
        ];
    }

    /**
     * @dataProvider fileUrlDataProvider
     *
     * @param string $localFile
     * @param string $directorySeparator
     * @param string $expectedFileUrl
     */
    public function testFilter($localFile, $directorySeparator, $expectedFileUrl)
    {
        $this->assertEquals($expectedFileUrl, FileUrlFilter::filter($localFile, $directorySeparator));
    }
}
