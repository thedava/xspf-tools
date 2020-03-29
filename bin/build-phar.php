<?php

require_once __DIR__ . '/../vendor/autoload.php';

$pharFile = __DIR__ . '/../build/xspf.phar';
$version = Xspf\Utils::getVersion();

// Dirty checks
if (is_dir(__DIR__ . '/../vendor/phpunit')) {
    echo 'Attention: dev-dependencies detected!', PHP_EOL;
}
if (Xspf\Utils::PERFORMANCE_TRACKING_ENABLED) {
    echo 'Attention: Performance tracking is enabled!', PHP_EOL;
}

if (file_exists($pharFile)) {
    echo 'Old file size: ', round(filesize($pharFile) / 1024, 2), ' kB', PHP_EOL;
    unlink($pharFile);
}

$phar = new Phar($pharFile);
$phar->setMetadata(['version' => $version]);
$phar->buildFromDirectory(dirname(__DIR__), '/(VERSION|src|vendor|console\.php|data)/');
$phar->setStub($phar->createDefaultStub('console.php'));
$phar->compressFiles(Phar::GZ);

echo 'File size: ', round(filesize($pharFile) / 1024, 2), ' kB', PHP_EOL;
