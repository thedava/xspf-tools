<?php

namespace Xspf\Commands\Index;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Commands\AbstractCommand;
use Xspf\WhiteAndBlacklistProviderTrait;

class CreateIndexPlaylistCommand extends AbstractCommand
{
    use WhiteAndBlacklistProviderTrait;

    protected function configure()
    {
        $this->setName('index:create-playlist')
            ->setDescription('Create an index file and directly convert it to a playlist file')
            ->setHelp(implode(PHP_EOL, [
                'This command creates an index file and directly converts it into a playlist-file',
            ]))
            ->addArgument('playlist-file', InputArgument::REQUIRED, 'The playlist file that should be created')
            ->addArgument('file-or-folder', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files and folders that should be added')
            ->addOption('index-folder', 'i', InputOption::VALUE_REQUIRED, 'The path of the index folder', 'index')
            ->addOption('order', '', InputOption::VALUE_REQUIRED, 'Order the index file (asc, desc, random)', null)
            ->appendWhiteAndBlacklistOptions($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->parseWhiteAndBlacklist($input);
        $app = $this->getApplication();
        $playlistFile = $input->getArgument('playlist-file');
        $indexFile = dirname($playlistFile) . '/' . $input->getOption('index-folder') . '/' . basename($playlistFile, '.xspf') . '.xd';

        try {
            file_exists(dirname($indexFile)) || @mkdir(dirname($indexFile), 0777, true);
        } catch (\Exception $e) {
            // nothing
        }

        // Create index (in a sub-directory)
        $arrayInput = new ArrayInput([
            'file-or-folder' => $input->getArgument('file-or-folder'),
            '--output'       => $indexFile,
            '--whitelist'    => $input->getOption('whitelist'),
            '--blacklist'    => $input->getOption('blacklist'),
        ]);
        $exitCode = $app->get('index:create')->run($arrayInput, $output);
        if ($exitCode != 0) {
            return $exitCode;
        }

        // Order index file
        if ($order = $input->getOption('order')) {
            $arrayInput = new ArrayInput([
                'order-type' => $order,
                'index-file' => $indexFile,
            ]);
            $exitCode = $app->get('index:order')->run($arrayInput, $output);
            if ($exitCode != 0) {
                return $exitCode;
            }
        }

        // Convert index file to playlist file
        $arrayInput = new ArrayInput([
            'index-file'    => $indexFile,
            'playlist-file' => $playlistFile,
        ]);
        $exitCode = $app->get('index:convert')->run($arrayInput, $output);
        if ($exitCode != 0) {
            return $exitCode;
        }

        return 0;
    }
}
