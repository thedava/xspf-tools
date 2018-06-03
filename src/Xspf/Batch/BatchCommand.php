<?php

namespace Xspf\Batch;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Xspf\AbstractCommand;

class BatchCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('batch')
            ->setDescription('Perform multiple xspf operations as a batch')
            ->addArgument('file', InputArgument::REQUIRED, 'A .yml file')
            ->addOption('validate', '', InputOption::VALUE_NONE, 'Validate the batch file instead of executing it')
            ->addOption('example', '', InputOption::VALUE_NONE, 'Create an example batch file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        if ($input->getOption('example')) {
            $output->writeln('<cyan>Creating example file</cyan>');

            if (file_exists($file)) {
                $this->getErrorOutput($output)->writeln('<red>The target file already exists!</red>');

                return 1;
            }

            file_put_contents($file, Yaml::dump([
                [
                    'command' => 'test',
                    'param1'  => 'value1',
                    'param2'  => [
                        '*.db',
                        '*.DS_Store',
                    ],
                ],
                [
                    'command' => 'test',
                    'param1'  => 'value1',
                    'param2'  => [
                        '*.db',
                        '*.DS_Store',
                    ],
                ],
            ]));
            $output->writeln('<green>Example file created</green>');

            return 0;
        }

        $yaml = Yaml::parse(file_get_contents($file));
        if ($input->getOption('validate')) {
            $output->writeln('<cyan>Validating file</cyan>');
            $stderr = $this->getErrorOutput($output);

            if (!is_array($yaml)) {
                $stderr->writeln(sprintf('<red>Expected array, %s given</red>', gettype($yaml)));

                return 1;
            }

            foreach ($yaml as $i => $data) {
                if (!is_array($data)) {
                    $stderr->writeln(sprintf('<red>Expected data #%d to be array, %s given</red>', $i + 1, gettype($yaml)));

                    return 1;
                } elseif (!array_key_exists('command', $data)) {
                    $stderr->writeln(sprintf('<red>Missing key "command" in data #%d</red>', $i + 1));
                } elseif (!is_string($data['command'])) {
                    $stderr->writeln(sprintf('<red>Expected key "command" in data #%d to be string, %s given</red>', $i + 1, gettype($data['command'])));
                }
            }

            $output->writeln('<green>File successfully validated</green>');

            return 0;
        }

        $errorOccurred = false;
        $app = $this->getApplication();
        foreach ($yaml as $data) {
            $output->writeln('');
            $output->writeln('<cyan>Executing command "' . $data['command'] . '"</cyan>');

            $command = $app->find($data['command']);
            $input = new ArrayInput($data);
            $exitCode = (int)$command->run($input, $output);

            $message = '-- exited with code ' . $exitCode;
            if ($exitCode != 0) {
                $errorOccurred = true;
                $message = '<yellow>' . $message . '</yellow>';
            } else {
                $message = '<green>' . $message . '</green>';
            }
            $output->writeln($message);
        }

        return ($errorOccurred) ? 1 : 0;
    }
}
