<?php

use Xspf\AbstractCommand;

require_once __DIR__ . '/vendor/autoload.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
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

    echo 'done', PHP_EOL;
}
catch (Exception $error) {
    echo 'An unexpected error occured!', PHP_EOL, PHP_EOL;
    echo $error->getMessage(), PHP_EOL;
    echo $error->getTraceAsString(), PHP_EOL;
    exit(1);
}
