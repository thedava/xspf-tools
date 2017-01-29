<?php

require_once __DIR__ . '/vendor/autoload.php';

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Determine order type
$orderType = (isset($argv[1]) && in_array($argv[1], \XspfOrder\AbstractOrderType::getOrderTypes()))
    ? $argv[1] : null;

// Determine file
$fileName = (isset($argv[2]) && file_exists($argv[2]) && filesize($argv[2]) > 32)
    ? $argv[2] : null;

if ($fileName === null) {
    $phar = Phar::running(false);

    if (!empty($phar)) {
        $cwd = dirname($phar) . DIRECTORY_SEPARATOR;
        if ($fileName === null && isset($argv[2]) && file_exists($cwd . $argv[2]) && filesize($cwd . $argv[2]) > 32) {
            $fileName = realpath($cwd . $argv[2]);
        }
    }
}

// Display a nice message
if ($orderType === null || $fileName === null) {
    echo 'Version ', trim(file_get_contents(__DIR__ . '/VERSION')), PHP_EOL, PHP_EOL;

    $isHelp = count($argv) <= 1 || isset($argv[1]) && in_array($argv[1], ['--help', '-h', '/h', '/?']);
    if (!$isHelp) {
        echo 'Following error(s) occurred: ', PHP_EOL;
        if ($orderType === null) {
            echo 'Unknown or invalid order type given!', PHP_EOL;
        }
        if ($fileName === null) {
            echo 'The given file does not exist or is empty!', PHP_EOL;
        }
        echo PHP_EOL;
    }

    echo 'Usage: php ', $argv[0], ' <order_type> <playlist_file>', PHP_EOL, PHP_EOL;

    echo 'Order Types:', PHP_EOL;
    echo '    asc:    The file will be ordered by video file names in ascending order', PHP_EOL;
    echo '    desc:   The file will be ordered by video file names in descending order', PHP_EOL;
    echo '    random: The file will be ordered in random order', PHP_EOL;

    echo PHP_EOL;
    exit(1);
}

try {
    $order = \XspfOrder\AbstractOrderType::factory($orderType);
    $file = new \XspfOrder\File($fileName);
    $file->load();
    $order->order($file);
    $file->save();

    echo 'done', PHP_EOL;
}
catch (Exception $error) {
    echo 'An unexpected error occured!', PHP_EOL, PHP_EOL;
    echo $error->getMessage(), PHP_EOL;
    echo $error->getTraceAsString(), PHP_EOL;
}
