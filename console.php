<?php

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Xspf\Utils;

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
//elseif (!function_exists('fnmatch')) {
//    echo $applicationTitle, PHP_EOL,
//    PHP_EOL,
//    'fnmatch() is not supported by your system',
//    PHP_EOL;
//    exit(1);
//}

require_once __DIR__ . '/vendor/autoload.php';
Utils::setDirectory(__DIR__);

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    // Styling and coloring
    $output = new ConsoleOutput();
    $formatter = $output->getFormatter();
    $formatter->setStyle('cyan', new OutputFormatterStyle('cyan'));
    $formatter->setStyle('green', new OutputFormatterStyle('green'));
    $formatter->setStyle('red', new OutputFormatterStyle('red'));
    $formatter->setStyle('yellow', new OutputFormatterStyle('yellow'));
    $formatter->setStyle('blue', new OutputFormatterStyle('blue'));

    $application = new \Symfony\Component\Console\Application($applicationTitle, Utils::getVersion());

    // Append commands
    foreach (require_once __DIR__ . '/data/console-commands.php' as $command) {
        $application->add(new $command());
    }

    $application->run(null, $output);
    exit(0);
} catch (Exception $error) {
    echo 'An unexpected error occured!', PHP_EOL, PHP_EOL;
    echo $error->getMessage(), PHP_EOL;
    echo $error->getTraceAsString(), PHP_EOL;
    exit(1);
}
