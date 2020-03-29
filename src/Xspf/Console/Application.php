<?php

namespace Xspf\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Utils;

class Application
{
    /** @var ConsoleApplication */
    protected $consoleApplication;

    /**
     * @return string
     */
    protected function getApplicationTitle(): string
    {
        return 'XSPF Tools';
    }

    /**
     * @param ConsoleApplication $application
     */
    protected function appendCommands(ConsoleApplication $application)
    {
        // Append commands
        foreach (require_once __DIR__ . '/../../../data/console-commands.php' as $command) {
            $application->add(new $command());
        }
    }

    /**
     * @return ConsoleApplication
     */
    public function create(): ConsoleApplication
    {
        if ($this->consoleApplication === null) {
            $this->consoleApplication = new ConsoleApplication($this->getApplicationTitle(), Utils::getVersion());
            $this->appendCommands($this->consoleApplication);
        }

        return $this->consoleApplication;
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput(): OutputInterface
    {
        // Styling and coloring
        $output = new ConsoleOutput();

        $formatter = $output->getFormatter();
        $formatter->setStyle('cyan', new OutputFormatterStyle('cyan'));
        $formatter->setStyle('green', new OutputFormatterStyle('green'));
        $formatter->setStyle('red', new OutputFormatterStyle('red'));
        $formatter->setStyle('yellow', new OutputFormatterStyle('yellow'));
        $formatter->setStyle('blue', new OutputFormatterStyle('blue'));

        return $output;
    }

    /**
     * @return int
     *
     * @throws \Exception
     */
    public function run(): int
    {
        return (int)$this->create()->run(null, $this->getOutput());
    }
}
