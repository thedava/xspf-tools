<?php

namespace Xspf\Console\Command;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\Utils;

class CleanCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('clean')
            ->setDescription('Removes all backup files');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (glob('*.bak') as $file) {
            $localFile = new Utils\LocalFile($file);
            try {
                $localFile->toXspfFile()->load();
                $localFile->delete();
                echo 'Removed "', $localFile->basename(), PHP_EOL;
            } catch (Exception $error) {
                echo 'Skipped file "', $localFile->basename(), '"', PHP_EOL;
            }
        }

        return 0;
    }
}
