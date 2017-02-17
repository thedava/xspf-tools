<?php

namespace Xspf\Create;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;

class CreateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Create a new playlist')
            ->addArgument('playlist-file', InputArgument::REQUIRED, 'The playlist file that should be created')
            ->addArgument('file-or-folder', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and folders that should be added');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
