<?php

namespace Xspf\Console\Command;

use DateTime;
use DavaHome\SelfUpdate\AssetFileDownloader;
use Exception;
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
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update')
            ->addOption('beta', 'b', InputOption::VALUE_NONE, 'Include betas');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');
        $beta = $input->getOption('beta');

        if ($beta) {
            $output->writeln('<yellow>BETA Channel</yellow>');
        }

        $output->writeln('');
        $currentVersion = Utils::getVersion();
        $output->writeln('Current Version: ' . $currentVersion);
        $output->writeln('');

        // Parse composer.json
        $composerJson = Utils::getComposerJson();

        [$owner, $repository] = explode('/', $composerJson['name']);
        $assetFileDownloader = new AssetFileDownloader($owner, $repository);

        $releaseInformation = ($beta)
            ? $assetFileDownloader->getMostRecentReleaseInformation()
            : $assetFileDownloader->getReleaseInformation();

        $date = new DateTime($releaseInformation['published_at']);
        $output->writeln('Latest available:');
        $output->writeln('Version: <cyan>' . $releaseInformation['tag_name'] . '</cyan>');
        $output->writeln('Published: ' . $date->format('Y-m-d H:i:s'));
        $output->writeln('Stability: ' . ($releaseInformation['prerelease'] ? '<yellow>BETA</yellow>' : '<green>stable</green>'));

        if (!$force && version_compare($currentVersion, $releaseInformation['tag_name'], '>=')) {
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
            if (stripos($data, $composerJson['name']) === false) {
                throw new Exception('The downloaded file is invalid! Please try again later or download it manually.');
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
