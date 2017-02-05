<?php

namespace Xspf\Help;

use Xspf\AbstractCommand;

class HelpCommand extends AbstractCommand
{
    public function isHelpCommand()
    {
        return true;
    }

    public function invoke()
    {
        throw new \Exception('You can\'t invoke the help command!');
    }

    public function printUsage(\Exception $error = null)
    {

    }
}
