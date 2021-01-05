<?php

namespace IntegrationTest;

use AbstractCommandIntegrationTest;
use Exception;

class IndexCommandTest extends AbstractCommandIntegrationTest
{
    const TEST_FILE_INDEX = 'test/data/index.xd';
    const TEST_FILE_PLAYLIST = 'test/data/index.xspf';

    /**
     * @covers \Xspf\Console\Command\Index\CreateIndexCommand::run()
     *
     * @throws Exception
     */
    public function testIndexCreate()
    {
        // Invoke command
        $output = $this->runCommand('index:create', [
            '-o'             => self::TEST_FILE_INDEX,
            'file-or-folder' => './bin',
        ]);

        // Validate console output and if file exists
        $this->assertStringContainsString('Index file successfully created', $output);
        $this->assertFileExists(self::TEST_FILE_INDEX, 'Newly created index file not found!');

        // Validate file content
        $content = file_get_contents(self::TEST_FILE_INDEX);
        $this->assertStringContainsString('build-console.php', $content);
        $this->assertStringContainsString('composer.sh', $content);
    }

    /**
     * @covers  \Xspf\Console\Command\Index\ConvertIndexCommand::run()
     * @depends testIndexCreate
     */
    public function testIndexConvert()
    {
        // Invoke command
        $output = $this->runCommand('index:convert', [
            'index-file'    => self::TEST_FILE_INDEX,
            'playlist-file' => self::TEST_FILE_PLAYLIST,
        ]);

        // Validate console output and if file exists
        $this->assertStringContainsString('Created playlist', $output);
        $this->assertFileExists(self::TEST_FILE_PLAYLIST, 'Newly created playlist file not found!');

        // Validate file content
        $content = file_get_contents(self::TEST_FILE_PLAYLIST);
        $this->assertStringContainsString('build-console.php', $content);
        $this->assertStringContainsString('composer.sh', $content);
    }

    /**
     * @covers  \Xspf\Console\Command\ValidateCommand::run()
     * @depends testIndexConvert
     */
    public function testValidate()
    {
        // Invoke command
        $output = $this->runCommand('validate', [
            '--stop-on-error' => true,
            'playlist-file'   => self::TEST_FILE_PLAYLIST,
        ]);

        // Validate console output and if file exists
        $this->assertStringContainsString('done', $output);
    }
}
