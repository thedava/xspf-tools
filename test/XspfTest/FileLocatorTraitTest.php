<?php

namespace XspfTest;

use Xspf\FileLocatorTrait;

class FileLocatorTraitTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileLocatorTrait */
    protected $fileLocatorTrait;

    public function setUp()
    {
        $this->fileLocatorTrait = $this->getMockForTrait(FileLocatorTrait::class, [], '', true, true, true, null);
    }

    public function skipFileDataProvider()
    {
        return [
            ['Thumbs.db', true],
            ['test.bak', true],
            ['Movie XYZ.mp4', false],
            ['FlashVideo.flv', false],
        ];
    }

    /**
     * @dataProvider skipFileDataProvider
     *
     * @param string $fileName
     * @param bool   $shouldBeSkipped
     */
    public function testShouldFileBeSkipped($fileName, $shouldBeSkipped)
    {
        $refObj = new \ReflectionObject($this->fileLocatorTrait);
        $refMethod = $refObj->getMethod('shouldFileBeSkipped');
        $refMethod->setAccessible(true);

        $this->assertEquals($shouldBeSkipped, $refMethod->invoke($this->fileLocatorTrait, $fileName));
    }
}
