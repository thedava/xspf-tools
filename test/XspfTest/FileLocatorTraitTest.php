<?php

namespace XspfTest;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionObject;
use Xspf\File\FileLocatorTrait;

class FileLocatorTraitTest extends TestCase
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
//            ['Thumbs.db', true],
//            ['test.bak', true],
['Movie XYZ.mp4', false],
['FlashVideo.flv', false],
        ];
    }

    /**
     * @dataProvider skipFileDataProvider
     *
     * @param string $fileName
     * @param bool   $shouldBeSkipped
     *
     * @throws ReflectionException
     */
    public function testShouldFileBeSkipped($fileName, $shouldBeSkipped)
    {
        $refObj = new ReflectionObject($this->fileLocatorTrait);
        $refMethod = $refObj->getMethod('shouldFileBeSkipped');
        $refMethod->setAccessible(true);

        $this->assertEquals($shouldBeSkipped, $refMethod->invoke($this->fileLocatorTrait, $fileName));
    }
}
