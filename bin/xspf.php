<?php

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Xspf\Utils;

ini_set('display_errors', 'On');
error_reporting(-1);

require_once __DIR__ . '/../vendor/autoload.php';

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

if (!function_exists('fnmatch')) {
    defined('FNM_NOESCAPE') || define('FNM_NOESCAPE', 1);
    defined('FNM_PATHNAME') || define('FNM_PATHNAME', 2);
    defined('FNM_PERIOD') || define('FNM_PERIOD', 4);
    defined('FNM_CASEFOLD') || define('FNM_CASEFOLD', 16);

    /**
     * Match filename against a pattern
     *
     * @link http://php.net/manual/en/function.fnmatch.php
     *
     * @param string $pattern
     * @param string $string
     * @param int    $flags [optional]
     *
     * @return bool true if there is a match, false otherwise.
     */
    function fnmatch($pattern, $string, $flags = 0)
    {
        if ($flags & FNM_CASEFOLD) {
            $pattern = mb_strtolower($pattern);
            $string = mb_strtolower($string);
        }

        return preg_match('#^' . strtr(preg_quote($pattern, '#'), ['\*' => '.*', '\?' => '.']) . '$#i', $string);
    }
}

try {
    // Styling and coloring
    $output = new ConsoleOutput();
    $formatter = $output->getFormatter();
    $formatter->setStyle('cyan', new OutputFormatterStyle('cyan'));
    $formatter->setStyle('green', new OutputFormatterStyle('green'));
    $formatter->setStyle('red', new OutputFormatterStyle('red'));
    $formatter->setStyle('yellow', new OutputFormatterStyle('yellow'));
    $formatter->setStyle('blue', new OutputFormatterStyle('blue'));

    $application = new \Symfony\Component\Console\Application('XSPF Tools', Utils::getVersion());

    // Append commands
    foreach (require_once __DIR__ . '/../data/console-commands.php' as $command) {
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
