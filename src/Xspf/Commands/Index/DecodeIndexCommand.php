<?php

namespace Xspf\Commands\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Commands\CreateCommand;
use Xspf\Index\IndexModel;

class DecodeIndexCommand extends CreateCommand
{
    protected function configure()
    {
        $this->setName('index:decode')
            ->setHidden(true)
            ->setDescription('Convert an index file to plain text')
            ->addArgument('index-file', InputArgument::OPTIONAL, 'The index file', 'index.xd');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexModel = new IndexModel($input->getArgument('index-file'));
        $indexModel->load();

        $plainIndexModel = new IndexModel(basename($input->getArgument('index-file'), '.xd') . '.txt');
        $plainIndexModel->setFiles(iterator_to_array($indexModel->getFiles()));
        $plainIndexModel->savePlain();

        $output->writeln('done');
    }
}
