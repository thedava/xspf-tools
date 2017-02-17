<?php

require_once __DIR__ . '/../vendor/autoload.php';

$pharFile = __DIR__ . '/../xspf.phar';
$version = \Xspf\Utils::getVersion();

// Dirty checks
if (is_dir(__DIR__.'/../vendor/phpunit')) {
    echo 'Attention: dev-dependencies detected!', PHP_EOL;
}

if (file_exists($pharFile)) {
    echo 'Old file size: ', round(filesize($pharFile) / 1024, 2), ' kB', PHP_EOL;
    unlink($pharFile);
}

$phar = new Phar($pharFile);
$phar->setMetadata(['version' => $version]);
$phar->buildFromDirectory(dirname(__DIR__), '/(VERSION|src|vendor|xspf\.php|data)/');
$phar->setStub($phar->createDefaultStub('bin/xspf.php'));

echo 'File size: ', round(filesize($pharFile) / 1024, 2), ' kB', PHP_EOL;
