<?php

use Xspf\Utils;

ini_set('display_errors', 'On');
error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';

set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    $application = new \Symfony\Component\Console\Application('XSPF Tools', Utils::getVersion());

    // Append commands
    foreach (require_once __DIR__ . '/../data/console-commands.php' as $command) {
        $application->add(new $command());
    }

    $application->run();
    exit(0);
} catch (Exception $error) {
    echo 'An unexpected error occured!', PHP_EOL, PHP_EOL;
    echo $error->getMessage(), PHP_EOL;
    echo $error->getTraceAsString(), PHP_EOL;
    exit(1);
}
