<?php

namespace Xspf\File;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;

class CopyCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('copy')
            ->setDescription('A simple copy functionality')
            ->addArgument('source', InputArgument::REQUIRED)
            ->addArgument('target', InputArgument::REQUIRED)
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force override of existing files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('force') && file_exists($input->getArgument('target'))) {
            $this->getErrorOutput($output)->writeln('<red>Target file already exists</red>');

            return 1;
        }

        return copy($input->getArgument('source'), $input->getArgument('target')) ? 0 : 1;
    }
}
