<?php

namespace Xspf\Console\Command;

use DavaHome\SelfUpdate\AssetFileDownloader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Utils;

class SelfUpdateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('self-update')
            ->setDescription('Replace the current version of xspf with the latest from GitHub')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->hasOption('force') && $input->getOption('force');

        $assetFileDownloader = new AssetFileDownloader('thedava', 'xspf-tools');
        $releaseInformation = $assetFileDownloader->getReleaseInformation();
        $date = new \DateTime($releaseInformation['published_at']);
        $output->writeln('Version: ' . $releaseInformation['tag_name']);
        $output->writeln('Published: ' . $date->format('Y-m-d H:i:s'));

        if (!$force && version_compare(Utils::getVersion(), $releaseInformation['tag_name'], '>=')) {
            $output->writeln('You are already using the latest version');

            return 0;
        }

        $output->writeln('Starting download of xspf.phar...');
        file_put_contents('.xspf.phar', $assetFileDownloader->downloadAsset('xspf.phar'));
        $output->writeln('Finished');
        $output->writeln('');

        if (!$force) {
            $output->writeln('Validating downloaded file');
            $data = shell_exec('php .xspf.phar version');
            if (stripos($data, 'thedava/xspf-tools') === false) {
                throw new \Exception('The downloaded file is invalid! Please try again later or download it manually.');
            }
        } else {
            $output->writeln('<comment>Skipped validation</comment>');
        }
        // Everything is okay. Rename it
        rename('.xspf.phar', 'xspf.phar');
        $output->writeln('<info>done</info>');

        return 0;
    }
}
