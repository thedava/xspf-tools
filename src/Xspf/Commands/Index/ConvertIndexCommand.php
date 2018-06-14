<?php

namespace Xspf\Commands\Index;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Commands\CreateCommand;
use Xspf\Index\IndexModel;
use Xspf\Track;

class ConvertIndexCommand extends CreateCommand
{
    protected function configure()
    {
        $this->setName('index:convert')
            ->setDescription('Convert an index file into a playlist file')
            ->addArgument('index-file', InputArgument::OPTIONAL, 'The index file', 'index.xd')
            ->addArgument('playlist-file', InputArgument::OPTIONAL, 'The playlist file that should be created', 'index.xspf')
            ->addOption('distinct', 't', InputOption::VALUE_NONE, 'Avoid duplicates in playlist file')
            ->addOption('delete', 'D', InputOption::VALUE_NONE, 'Remove index file after conversion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexModel = new IndexModel($input->getArgument('index-file'));
        $indexModel->load();

        $skipCount = 0;
        $history = new \ArrayObject();
        $distinct = $input->getOption('distinct');

        $tracks = [];
        foreach ($indexModel->getFiles() as $file) {
            if ($distinct) {
                if (isset($history[$file])) {
                    $skipCount++;
                    continue;
                }

                $history[$file] = 1;
            }

            $tracks[] = new Track($file);
        }

        $this->createPlaylist($input, $output, $tracks);
        $output->writeln('Created playlist with ' . count($tracks) . ' files');

        if ($skipCount > 0) {
            $output->writeln($skipCount . ' files were skipped due to distinct-option');
        }

        if ($input->getOption('delete')) {
            $indexModel->delete();
            $output->writeln('Deleted index file');
        }
    }
}
