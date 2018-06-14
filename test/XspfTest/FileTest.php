<?php

namespace XspfTest;

use Xspf\File\File;
use Xspf\Track;

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
