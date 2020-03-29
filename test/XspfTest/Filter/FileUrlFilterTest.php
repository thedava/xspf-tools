<?php

namespace XspfTest\Filter;

use PHPUnit\Framework\TestCase;
use Xspf\Filter\FileUrlFilter;

class FileUrlFilterTest extends TestCase
{
    public function fileUrlDataProvider()
    {
        return [
            //
            // Windows
            ['C:\Video\video.mp4', '\\', 'file:///C:/Video/video.mp4'],
            ['C:\Video\spaces video.mp4', '\\', 'file:///C:/Video/spaces%20video.mp4'],
            ['C:\Video\(strange) symbols video.mp4', '\\', 'file:///C:/Video/(strange)%20symbols%20video.mp4'],
            //
            // Windows (Cygwin)
            ['/cygdrive/c/Video/video.mp4', '/', 'file:///C:/Video/video.mp4'],
            ['/cygdrive/c/Video/spaces video.mp4', '/', 'file:///C:/Video/spaces%20video.mp4'],
            ['/cygdrive/c/Video/(strange) symbols video.mp4', '/', 'file:///C:/Video/(strange)%20symbols%20video.mp4'],
            //
            // Linux
            ['/home/user/video/video.mp4', '/', 'file:///home/user/video/video.mp4'],
            ['/home/user/video/spaces video.mp4', '/', 'file:///home/user/video/spaces%20video.mp4'],
            ['/home/user/video/(strange) symbols video.mp4', '/', 'file:///home/user/video/(strange)%20symbols%20video.mp4'],
            //
            // Official examples
            ['/mp3s/Yo La Tengo/5_Nuclear War Version 4 (Mike Ladd Remix).mp3', '/', 'file:///mp3s/Yo%20La%20Tengo/5_Nuclear%20War%20Version%204%20(Mike%20Ladd%20Remix).mp3'],
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
