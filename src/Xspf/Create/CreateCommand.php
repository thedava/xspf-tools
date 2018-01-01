<?php

namespace Xspf\Create;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;
use Xspf\File;
use Xspf\FileLocatorTrait;
use Xspf\Track;

class CreateCommand extends AbstractCommand
{
    use FileLocatorTrait;

    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Create a new playlist')
            ->addArgument('playlist-file', InputArgument::REQUIRED, 'The playlist file that should be created')
            ->addArgument('file-or-folder', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and folders that should be added');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tracks = [];
        foreach ($input->getArgument('file-or-folder') as $value) {
            foreach ($this->getFiles($value, $output) as $file) {
                $output->writeln('Adding ' . $file . ' as track', $output::VERBOSITY_DEBUG);
                try {
                    $tracks[] = new Track(realpath($file));
                } catch (\Exception $error) {
                    // nothing
                }
            }
        }

        $this->createPlaylist($input, $output, $tracks);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Track[]         $tracks
     */
    protected function createPlaylist(InputInterface $input, OutputInterface $output, array $tracks)
    {
        $output->writeln('Found ' . count($tracks) . ' files');

        (new File($input->getArgument('playlist-file')))
            ->setTracks($tracks)
            ->save(false);
    }
}
