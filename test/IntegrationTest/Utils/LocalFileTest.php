<?php

namespace IntegrationTest\Utils;

use PHPUnit\Framework\TestCase;
use Xspf\Utils\LocalFile;

class LocalFileTest extends TestCase
{
    const TEST_FILE_EXISTS = XSPF_TEMP_DIR . '/_LocalFile.txt';
    const TEST_FILE_MISSING = XSPF_TEMP_DIR . '/_LocalFile-MISSING.txt';

    protected function tearDown(): void
    {
        parent::tearDown();

        file_exists(self::TEST_FILE_EXISTS) && unlink(self::TEST_FILE_EXISTS);
        file_exists(self::TEST_FILE_MISSING) && unlink(self::TEST_FILE_MISSING);
    }


    /**
     * @return LocalFile[]
     */
    private function prepareLocalFiles(): array
    {
        $localFileExists = new LocalFile(self::TEST_FILE_EXISTS);
        $localFileExists->put('foo');

        $localFileMissing = new LocalFile(self::TEST_FILE_MISSING);
        $localFileMissing->delete();

        return [
            $localFileExists,
            $localFileMissing,
        ];
    }

    /**
     * @covers LocalFile::exists(), LocalFile::delete(), LocalFile::put()
     */
    public function testExists()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $this->assertTrue($exists->exists());
        $this->assertFalse($missing->exists());

        $exists->delete();
        $this->assertTrue($missing->put('bar'));

        $this->assertFalse($exists->exists());
        $this->assertTrue($missing->exists());
    }

    /**
     * @covers LocalFile::mtime()
     */
    public function testMtime()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $mtime = $exists->mtime();
        $this->assertIsInt($mtime);
        $this->assertGreaterThan(time() - 10, $mtime);

        $mtime = $missing->mtime();
        $this->assertNull($mtime);
    }

    /**
     * @covers LocalFile::size()
     */
    public function testSize()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $size = $exists->size();
        $this->assertIsInt($size);
        $this->assertGreaterThan(1, $size);

        $size = $missing->size();
        $this->assertIsInt($size);
        $this->assertEquals(-1, $size);
    }
}
