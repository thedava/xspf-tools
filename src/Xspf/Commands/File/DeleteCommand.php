<?php

namespace Xspf\Commands\File;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Commands\AbstractCommand;

class DeleteCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('file:delete')
            ->setDescription('A simple file delete functionality')
            ->addArgument('file', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        $output->writeln('Deleting file ' . $file, $output::VERBOSITY_VERBOSE);

        if (file_exists($file)) {
            unlink($file);
        }

        return 0;
    }
}
