<?php

namespace Xspf\Backup;

use Xspf\AbstractCommand;
use Xspf\File;
use Xspf\Utils;

class CleanCommand extends AbstractCommand
{
    /**
     * @return void
     */
    public function invoke()
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
    }

    /**
     * Print out the usage of this command
     *
     * @param \Exception $error
     *
     * @return void
     */
    public function printUsage(\Exception $error = null)
    {
        $this->printDescription('Removes all backup files');
        $this->printUsageCommand(['clean']);
    }
}
