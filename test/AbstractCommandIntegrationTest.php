<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Xspf\Console\Application;

abstract class AbstractCommandIntegrationTest extends TestCase
{
    /** @var Application */
    private static $application;

    /**
     * @param string $command
     *
     * @return Command
     */
    final protected function getCommand(string $command): Command
    {
        if (self::$application === null) {
            self::$application = new Application();
        }

        return self::$application
            ->create()
            ->get($command);
    }

    /**
     * @param string $command
     * @param array  $input
     * @param int    $expectedExitCode
     *
     * @return string Console output
     * @throws Exception
     */
    final protected function runCommand(string $command, array $input, int $expectedExitCode = 0): string
    {
        $bufferedOutput = new BufferedOutput();
        $exitCode = (int)$this->getCommand($command)->run(new ArrayInput($input), $bufferedOutput);
        $this->assertEquals($expectedExitCode, $exitCode, vsprintf('Expected command "%s" to exit with code %d, but code %d was given!', [
            $command,
            $expectedExitCode,
            $exitCode,
        ]));

        return $bufferedOutput->fetch();
    }
}
