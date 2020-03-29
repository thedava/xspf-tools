<?php

namespace IntegrationTest;

use AbstractCommandIntegrationTest;
use Exception;

class DuplicatesCommandTest extends AbstractCommandIntegrationTest
{
    const TEST_FILE_DUPLICATES = 'test/data/duplicates.txt';

    /**
     * @covers \Xspf\Console\Command\Duplicates\ListDuplicatesCommand::run()
     * @throws Exception
     */
    public function testDuplicatesList()
    {
        // Invoke command
        $output = $this->runCommand('duplicates:list', [
            'action' => 'path',
            'value'  => 'test/data/',
            '-o'     => self::TEST_FILE_DUPLICATES,
        ]);

        // Validate console output and if file exists
        $this->assertContains('Saved', $output);
        $this->assertContains('files to', $output);
        $this->assertFileExists(self::TEST_FILE_DUPLICATES, 'Newly created duplicates file not found!');

        // Validate file content
        $content = file_get_contents(self::TEST_FILE_DUPLICATES);
        $this->assertContains('.gitkeep', $content);
    }

    /**
     * @depends testDuplicatesList
     * @covers  \Xspf\Console\Command\Duplicates\ListDuplicatesCommand::run()
     * @throws Exception
     */
    public function testDuplicatesListAppend()
    {
        // Invoke command
        $args = [
            'action' => 'path',
            'value'  => 'test/data/',
            '-i'     => self::TEST_FILE_DUPLICATES,
            '-o'     => self::TEST_FILE_DUPLICATES,
        ];

        $this->runCommand('duplicates:list', $args);
        file_put_contents('test/data/test_1.txt', 'equal');
        file_put_contents('test/data/test_2.txt', 'equal');
        $this->runCommand('duplicates:list', $args);

        // Validate file content
        $this->assertFileExists(self::TEST_FILE_DUPLICATES, 'Newly created duplicates file not found!');
        $content = file_get_contents(self::TEST_FILE_DUPLICATES);
        $this->assertContains('test_1.txt', $content);
        $this->assertContains('test_2.txt', $content);

        // Remove one file and run command again. The file should still be in that list (append)
        $this->assertTrue(unlink('test/data/test_1.txt'));
        $this->runCommand('duplicates:list', $args);
        $this->assertFileExists(self::TEST_FILE_DUPLICATES, 'Newly created duplicates file not found!');
        $content = file_get_contents(self::TEST_FILE_DUPLICATES);
        $this->assertContains('test_1.txt', $content);
        $this->assertContains('test_2.txt', $content);
        $this->assertContains('duplicates.txt', $content);

        // Create second file again
        file_put_contents('test/data/test_2.txt', 'equal');
    }

    /**
     * @depends testDuplicatesListAppend
     * @covers  \Xspf\Console\Command\Duplicates\ShowDuplicatesCommand::run()
     * @throws Exception
     */
    public function testDuplicatesShow()
    {
        // Invoke command
        $output = $this->runCommand('duplicate:show', [
            'file'          => self::TEST_FILE_DUPLICATES,
            '--no-progress' => true,
        ]);

        // Validate file content
        $this->assertContains('test_1.txt', $output);
        $this->assertContains('test_2.txt', $output);
        $this->assertNotContains('duplicates.txt', $output);
    }
}
