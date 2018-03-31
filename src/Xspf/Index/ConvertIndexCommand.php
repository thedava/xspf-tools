<?php

namespace Xspf\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Create\CreateCommand;
use Xspf\Track;

class ConvertIndexCommand extends CreateCommand
{
    protected function configure()
    {
        $this->setName('convert-index')
            ->setDescription('Convert an index file into a playlist file')
            ->addArgument('index-file', InputArgument::OPTIONAL, 'The index file', 'index.xd')
            ->addArgument('playlist-file', InputArgument::OPTIONAL, 'The playlist file that should be created', 'index.xspf');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexModel = new IndexModel($input->getArgument('index-file'));
        $indexModel->load();

        $tracks = [];
        foreach ($indexModel->getFiles() as $file) {
            $tracks[] = new Track($file);
        }

        $this->createPlaylist($input, $output, $tracks);
    }
}
