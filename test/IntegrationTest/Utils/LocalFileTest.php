<?php

namespace IntegrationTest\Utils;

use PHPUnit\Framework\TestCase;
use Xspf\Utils\LocalFile;

class LocalFileTest extends TestCase
{
    private const TEST_FILE_EXISTS = XSPF_TEMP_DIR . '/_LocalFile.txt';
    private const TEST_FILE_MISSING = XSPF_TEMP_DIR . '/_LocalFile-MISSING.txt';

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
     * @covers LocalFile::mtime(), LocalFile::mtimeReadable()
     */
    public function testMtime()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $mtime = $exists->mtime();
        $this->assertIsInt($mtime);
        $this->assertGreaterThan(time() - 10, $mtime);
        $this->assertIsString($exists->mtimeReadable());
        $this->assertNotEquals('?', $exists->mtimeReadable());

        $mtime = $missing->mtime();
        $this->assertNull($mtime);
        $this->assertIsString($missing->mtimeReadable());
        $this->assertEquals('?', $missing->mtimeReadable());
    }

    /**
     * @covers LocalFile::size(), LocalFile::sizeReadable()
     */
    public function testSize()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $size = $exists->size();
        $this->assertIsInt($size);
        $this->assertGreaterThan(1, $size);
        $this->assertIsString($exists->sizeReadable());
        $this->assertNotEquals('? MB', $exists->sizeReadable());

        $size = $missing->size();
        $this->assertNull($size);
        $this->assertIsString($missing->sizeReadable());
        $this->assertEquals('? MB', $missing->sizeReadable());
    }

    /**
     * @covers LocalFile::basename()
     */
    public function testBasename()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $this->assertSame(basename(self::TEST_FILE_EXISTS), $exists->basename());
        $this->assertSame(basename(self::TEST_FILE_MISSING), $missing->basename());
    }

    /**
     * @covers LocalFile::force()
     */
    public function testForce()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $this->assertTrue($exists->force());
        $this->assertTrue($exists->exists());

        $this->assertTrue($missing->force());
        $this->assertFalse($missing->exists());
    }

    /**
     * @covers LocalFile::touch()
     */
    public function testTouch()
    {
        [, $missing] = $this->prepareLocalFiles();

        $this->assertFalse($missing->exists());
        $this->assertTrue($missing->touch());
        $this->assertTrue($missing->exists());
    }

    /**
     * @covers LocalFile::validate()
     */
    public function testValidate()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $exists->validate();

        $this->expectExceptionMessageMatches(sprintf('/%s/', $missing->basename()));
        $missing->validate();
    }

    /**
     * @covers LocalFile::read(), LocalFile::put()
     */
    public function testRead()
    {
        [$exists, $missing] = $this->prepareLocalFiles();

        $this->assertNull($missing->read());
        $missing->put('phpunit_02');
        $this->assertSame('phpunit_02', $missing->read());

        $exists->put('phpunit_03');
        $this->assertSame('phpunit_03', $exists->read());
    }
}
