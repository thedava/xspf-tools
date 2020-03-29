<?php

namespace Xspf\Console\Command;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\File\File;

/**
 * @composer "james-heinrich/getid3": "^1.9"
 */
class UpdateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('update')
            ->setEnabled(false)
            ->setDescription('Update all entries (duration, etc.)')
            ->addArgument('playlist-file', InputArgument::REQUIRED, 'The playlist file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = new File($input->getArgument('playlist-file'));
        $file->load();

        $progress = new ProgressBar($output, count($file->getTracks()));
        $progress->setRedrawFrequency(1);
        $progress->setFormat('very_verbose');
        $progress->start();

        $id3 = new \getID3();
        foreach ($file->getTracks() as $track) {
            $result = $id3->analyze($track->getLocation());
            $track->setDuration(isset($result['playtime_seconds']) ? (int)$result['playtime_seconds'] : $track->getDuration());

            $progress->advance();
        }

        $progress->finish();
        $file->save();
    }
}
