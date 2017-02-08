<?php

namespace Xspf\Create;


use Xspf\AbstractCommand;

class CreateCommand extends AbstractCommand
{
    /**
     * @return void
     */
    public function invoke()
    {
        throw new \Exception('Not implemented yet!');
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
        $this->printDescription('Create a new playlist');
        $this->printUsageCommand(['create', '<playlist_file>', '<file_or_folder>...']);
    }
}
