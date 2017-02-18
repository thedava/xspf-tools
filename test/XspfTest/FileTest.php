<?php

namespace XspfTest;

use Xspf\File;
use Xspf\Track;
use Xspf\XspfSchemeValidator;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testSave()
    {
        $file = new File(XSPF_TEMP_DIR . '/file_test.xml');
        $file->setTracks([
            (new Track(__FILE__))
                ->setDuration(123),
        ]);

        // Create first file
        $file->save();
        $this->assertCount(1, $file->getTracks());
        $this->assertFileExists($file->getFileName());
        $this->assertFileNotExists($file->getFileName() . File::BACKUP_SUFFIX);
        $result = file_get_contents($file->getFileName());
        $this->assertTrue(XspfSchemeValidator::isValid($result));

        // Create backup
        $file->save();
        $this->assertFileExists($file->getFileName());
        $this->assertFileExists($file->getFileName() . File::BACKUP_SUFFIX);

        // Create backup of backup
        $file->save();
        $this->assertFileExists($file->getFileName());
        $this->assertFileExists($file->getFileName() . File::BACKUP_SUFFIX);
    }
}
