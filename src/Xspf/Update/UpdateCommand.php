<?php

namespace Xspf\Update;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;
use Xspf\File;

class UpdateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('update')
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

        foreach ($file->getTracks() as $track) {
            $track->update();
            $progress->advance();
        }

        $progress->finish();
        $file->save();
    }
}
