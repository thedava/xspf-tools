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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('XSPF Tools v' . Utils::getVersion());
        $output->writeln('Build: ' . date('Y-m-d', filemtime(Utils::getVersionFile())));

        $output->writeln('');
        if ($output->isVerbose() && Utils::isPhar()) {
            $output->writeln('Compiled: ' . date('Y-m-d H:i:s', filemtime(Phar::running(false))));
        } else {
            $output->writeln('GitHub: thedava/xspf-tools');
            $output->writeln('https://github.com/thedava/xspf-tools');
        }
    }
}
