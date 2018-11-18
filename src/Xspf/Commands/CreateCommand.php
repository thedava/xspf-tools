<?php

namespace Xspf\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\File\File;
use Xspf\File\FileLocatorTrait;
use Xspf\Order\AbstractOrderType;
use Xspf\Track;
use Xspf\WhiteAndBlacklistProviderTrait;

class CreateCommand extends AbstractCommand
{
    use FileLocatorTrait;
    use WhiteAndBlacklistProviderTrait;

    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Create a new playlist')
            ->addArgument('playlist-file', InputArgument::REQUIRED, 'The playlist file that should be created')
            ->addArgument('file-or-folder', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and folders that should be added')
            ->addOption('order', '', InputOption::VALUE_REQUIRED, 'Order the index file (asc, desc, random)', null);
        $this->appendWhiteAndBlacklistOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating ' . $input->getArgument('playlist-file'), $output::VERBOSITY_VERBOSE);
        $this->parseWhiteAndBlacklist($input);

        $tracks = [];
        foreach ((array)$input->getArgument('file-or-folder') as $value) {
            foreach ($this->getFiles($value, $output) as $file) {
                if ($this->isFileAllowed($file)) {
                    $output->writeln('Adding ' . $file . ' as track', $output::VERBOSITY_DEBUG);
                    try {
                        $tracks[] = new Track(realpath($file));
                    } catch (\Exception $error) {
                        // nothing
                    }
                } elseif ($output->isVeryVerbose()) {
                    $output->writeln('Skipping ' . $file . ' due to white/blacklist');
                }
            }
        }

        $this->createPlaylist($input, $output, $tracks, $input->getOption('order'));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Track[]         $tracks
     * @param string|null     $order
     *
     * @throws \Exception
     */
    protected function createPlaylist(InputInterface $input, OutputInterface $output, array $tracks, $order = null)
    {
        $output->writeln('Found ' . count($tracks) . ' files');

        $file = (new File($input->getArgument('playlist-file')))->setTracks($tracks);

        if ($order !== null) {
            $orderType = AbstractOrderType::factory($order);
            $orderType->orderFile($file);
        }

        $file->save(false);
    }
}
