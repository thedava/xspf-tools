<?php

namespace IntegrationTest;

use AbstractCommandIntegrationTest;
use Exception;

class DuplicatesCommandTest extends AbstractCommandIntegrationTest
{
    const TEST_FILE_DUPLICATES = 'test/data/duplicates.txt';

    /**
     * @covers \Xspf\Console\Command\Duplicates\ListDuplicatesCommand::run()
     *
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
        $this->assertStringContainsString('Saved', $output);
        $this->assertStringContainsString('files to', $output);
        $this->assertFileExists(self::TEST_FILE_DUPLICATES, 'Newly created duplicates file not found!');

        // Validate file content
        $content = file_get_contents(self::TEST_FILE_DUPLICATES);
        $this->assertStringContainsString('.gitkeep', $content);
    }

    /**
     * @depends testDuplicatesList
     * @covers  \Xspf\Console\Command\Duplicates\ListDuplicatesCommand::run()
     *
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
        file_put_contents('test/data/test_1_1.txt', 'equal');
        file_put_contents('test/data/test_1_2.txt', 'equal');
        $this->runCommand('duplicates:list', $args);

        // Validate file content
        $this->assertFileExists(self::TEST_FILE_DUPLICATES, 'Newly created duplicates file not found!');
        $content = file_get_contents(self::TEST_FILE_DUPLICATES);
        $this->assertStringContainsString('test_1_1.txt', $content);
        $this->assertStringContainsString('test_1_2.txt', $content);

        // Remove one file and run command again. The file should still be in that list (append)
        $this->assertTrue(unlink('test/data/test_1_1.txt'));
        $this->runCommand('duplicates:list', $args);
        $this->assertFileExists(self::TEST_FILE_DUPLICATES, 'Newly created duplicates file not found!');
        $content = file_get_contents(self::TEST_FILE_DUPLICATES);
        $this->assertStringContainsString('test_1_1.txt', $content);
        $this->assertStringContainsString('test_1_2.txt', $content);
        $this->assertStringContainsString('duplicates.txt', $content);

        // Create second file again
        file_put_contents('test/data/test_1_2.txt', 'equal');
    }

    /**
     * @depends testDuplicatesListAppend
     * @covers  \Xspf\Console\Command\Duplicates\ShowDuplicatesCommand::run()
     *
     * @throws Exception
     */
    public function testDuplicatesShowChecksum()
    {
        // Invoke command
        $output = $this->runCommand('duplicate:show', [
            'files'         => self::TEST_FILE_DUPLICATES,
            '--no-progress' => true,
        ]);

        // Validate file content
        $this->assertStringContainsString('test_1_1.txt', $output);
        $this->assertStringContainsString('test_1_2.txt', $output);
        $this->assertStringNotContainsString('duplicates.txt', $output);
    }

    public function testDuplicatesShowMultiple()
    {
        $files = [
            'test/data/duplicates_1_1.txt' => 'test/data/test_2_1.txt',
            'test/data/duplicates_1_2.txt' => 'test/data/test_2_2.txt',
            'test/data/duplicates_1_3.txt' => 'test/data/test_2_3.txt',
        ];

        foreach ($files as $outputFile => $testFile) {
            file_put_contents($testFile, 'equal');

            $this->runCommand('duplicates:list', [
                'action' => 'path',
                'value'  => 'test/data/',
                '-o'     => $outputFile,
            ]);
            $this->assertFileExists($outputFile, 'Newly created duplicates file not found!');

            unlink($testFile);
        }

        $output = $this->runCommand('duplicate:show', [
            'files'         => array_keys($files),
            '--no-progress' => true,
        ]);
        foreach (array_values($files) as $file) {
            $this->assertStringContainsString(basename($file), $output); // File exists only once but it occurs multiple times
        }
    }
}
