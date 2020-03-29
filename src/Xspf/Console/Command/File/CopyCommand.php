<?php

namespace Xspf\Console\Command\File;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Console\Command\AbstractCommand;

class CopyCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('file:copy')
            ->setAliases(['copy'])
            ->setDescription('A simple copy functionality')
            ->addArgument('source', InputArgument::REQUIRED)
            ->addArgument('target', InputArgument::REQUIRED)
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force override of existing files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');

        $output->writeln('Copying from ' . $source . ' to ' . $target, $output::VERBOSITY_VERBOSE);

        if (!$input->getOption('force') && file_exists($target)) {
            $this->getErrorOutput($output)->writeln('<red>Target file already exists</red>');

            return 1;
        }

        return copy($source, $target) ? 0 : 1;
    }
}
