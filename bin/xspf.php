<?php

use Xspf\AbstractCommand;
use Xspf\Utils;

ini_set('display_errors', 'On');
error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    echo 'XSPF Tools Version ', Utils::getVersion(), PHP_EOL, PHP_EOL;

    AbstractCommand::setArguments($argv);
    $cmd = AbstractCommand::getCommandArg();
    $command = AbstractCommand::factory($cmd);

    // Display help or invoke command
    if ($command->isHelpCommand()) {
        $command->printUsage();
        exit(0);
    } else {
        try {
            $command->invoke();
        }
        catch (\Exception $err) {
            $command->printUsage($err);
            exit(1);
        }
    }

    if (!($command instanceof \Xspf\Help\HelpCommand)) {
        echo 'done', PHP_EOL;
    }
}
catch (Exception $error) {
    echo 'An unexpected error occured!', PHP_EOL, PHP_EOL;
    echo $error->getMessage(), PHP_EOL;
    echo $error->getTraceAsString(), PHP_EOL;
    exit(1);
}
