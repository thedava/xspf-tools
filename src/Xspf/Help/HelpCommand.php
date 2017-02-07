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
        echo 'Usage: php ', $this->getExecutedFileName(), ' <command>', PHP_EOL;
        echo '       php ', $this->getExecutedFileName(), ' help <command>', PHP_EOL, PHP_EOL;

        echo 'Available commands: ', PHP_EOL;
        foreach (self::$map as $command => $class) {
            echo '    - ', $command, PHP_EOL;
        }
        echo PHP_EOL;
    }
}
