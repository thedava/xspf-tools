<?php

namespace Xspf\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\File\File;
use Xspf\File\FileLocatorTrait;
use Xspf\Track;

class MergeCommand extends AbstractCommand
{
    use FileLocatorTrait;

    protected function configure()
    {
        $this->setName('merge')
            ->setDescription('Merge multiple playlists into one single file')
            ->addOption('unique', 'u', InputOption::VALUE_NONE, 'Filter duplicate tracks')
            ->addArgument('target', InputArgument::REQUIRED, 'The target file which will contain all the tracks from the other files')
            ->addArgument('source', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The source files', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tracks = [];
        foreach ($input->getArgument('source') as $source) {
            foreach ($this->getFiles($source, $output) as $sourceFile) {
                $tracks = array_merge($tracks, (new File($sourceFile))->load()->getTracks());
            }
        }

        $target = $input->getArgument('target');
        if (file_exists($target)) {
            $tracks = array_merge($tracks, (new File($target))->load()->getTracks());
        }

        if ($input->getOption('unique')) {
            $filteredTracks = [];
            foreach ($tracks as $track) {
                /** @var Track $track */
                $filteredTracks[$track->getLocation()] = (isset($filteredTracks[$track->getLocation()]))
                    ? (($track->getDuration() > 0) ? $track : $filteredTracks[$track->getLocation()])
                    : $track;
            }

            $tracks = array_values($filteredTracks);
        }

        (new File($target))->setTracks($tracks)->save(false);
    }
}
