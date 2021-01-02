<?php

namespace Xspf\Console\Command;

use Phar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Utils;

class VersionCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('version')
            ->setDescription('Display the version of this xspf-tools');
    }

    /**
     * @return string
     */
    private function getVersion()
    {
        $composerJsonContent = file_get_contents(Utils::getDirectory() . '/composer.json');
        $composerJson = json_decode($composerJsonContent, true);

        $phpVersion = $composerJson['require']['php'];
        $match = [];
        preg_match('/[0-9]\.[0-9]/', $phpVersion, $match);

        return $match[0];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exitCode = 0;
        $output->writeln('XSPF Tools v' . Utils::getVersion());
        $output->writeln('Build: ' . date('Y-m-d', filemtime(Utils::getVersionFile())));

        // Show PHP Version
        $output->writeln('');
        $phpVersion = phpversion();
        $minPhpVersion = $this->getVersion();
        $output->writeln('Current PHP Version: ' . $phpVersion);
        if (version_compare($phpVersion, $minPhpVersion, '<')) {
            $exitCode = 1;
            $output->writeln('<red>Insufficient PHP Version!</red>');
            $output->writeln('Minimum required version: ' . $minPhpVersion);
        }

        // Show Build information
        $output->writeln('');
        if ($output->isVerbose() && Utils::isPhar()) {
            $output->writeln('Compiled: ' . date('Y-m-d H:i:s', filemtime(Phar::running(false))));
        } else {
            $output->writeln('GitHub: thedava/xspf-tools');
            $output->writeln('https://github.com/thedava/xspf-tools');
        }

        return $exitCode;
    }
}
