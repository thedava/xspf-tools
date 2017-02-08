<?php

namespace Xspf\Help;

use Xspf\AbstractCommand;

class HelpCommand extends AbstractCommand
{
    public function determineSubCommand()
    {
        return (isset(self::$arguments[2]) && isset(self::$map[self::$arguments[2]]))
            ? self::$arguments[2] : null;
    }

    public function invoke()
    {
        $subCommand = $this->determineSubCommand();

        // Print help for subCommand
        if ($subCommand !== null) {
            $subCmdObj = static::factory($subCommand);
            $subCmdObj->setIsHelpCommand(true);
            $subCmdObj->printUsage();
            return;
        }

        // Print general help
        $this->printUsage();
    }

    public function printUsage(\Exception $error = null)
    {
        $this->printDescription('A toolset for creating and manipulating XSPF playlists');

        $this->printUsageCommand(['<command>'], true, false);
        $this->printUsageCommand(['help', '<command>'], false);

        $this->printUsageList('Available commands', array_keys(self::$map));
    }
}
