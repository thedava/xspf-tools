<?php

namespace Xspf\Validate;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;
use Xspf\File;

class ValidateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('validate')
            ->setDescription('Checks if all files in the playlist exist and removes the missing files')
            ->addArgument('playlist-file', InputArgument::REQUIRED, 'The playlist file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = new File($input->getArgument('playlist-file'));
        $file->load();

        $tracks = [];
        foreach ($file->getTracks() as $track) {
            if (file_exists($track->getLocation())) {
                $tracks[] = $track;
                $output->writeln('<info>File "' . basename($track->getLocation()) . '" exists</info>', $output::VERBOSITY_VERBOSE);
            } else {
                $output->writeln('<error>File "' . basename($track->getLocation()) . '" is missing and will be removed');
            }
        }

        $file->setTracks($tracks);
        $file->save();
    }

}
