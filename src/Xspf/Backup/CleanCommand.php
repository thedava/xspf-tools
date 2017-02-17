<?php

namespace Xspf\Backup;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xspf\AbstractCommand;
use Xspf\File;
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = Utils::determinePath('./');
        foreach (glob($path . DIRECTORY_SEPARATOR . '*.bak') as $file) {
            try {
                (new File($file))->load();
                unlink($file);
                echo 'Removed "', basename($file), PHP_EOL;
            } catch (\Exception $error) {
                echo 'Skipped file "', basename($file), '"', PHP_EOL;
            }
        }

        return 0;
    }
}
