<?php

ini_set('display_errors', 'On');
error_reporting(-1);

$applicationTitle = 'XSPF Tools';
if (PHP_MAJOR_VERSION < 7) {
    echo $applicationTitle, PHP_EOL,
    PHP_EOL,
    'Minimum required version is 7.0 but ', phpversion(), ' was given',
    PHP_EOL;
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';
Xspf\Utils::setDirectory(__DIR__);

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    $application = new Xspf\Console\Application();

    exit($application->run());
} catch (Exception $error) {
    echo 'An unexpected error occured!', PHP_EOL, PHP_EOL;
    echo $error->getMessage(), PHP_EOL;
    echo $error->getTraceAsString(), PHP_EOL;
    exit(1);
}
