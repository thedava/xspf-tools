<?php

use Xspf\Utils;
use Xspf\Utils\LocalFile;

require_once __DIR__ . '/../vendor/autoload.php';

$pharFile = new LocalFile(__DIR__ . '/../build/xspf.phar');
$version = Utils::getVersion();

// Dirty checks
if (is_dir(__DIR__ . '/../vendor/phpunit')) {
    echo 'Attention: dev-dependencies detected!', PHP_EOL;
}
if (Utils::PERFORMANCE_TRACKING_ENABLED) {
    echo 'Attention: Performance tracking is enabled!', PHP_EOL;
}

if ($pharFile->exists()) {
    echo 'Old file size: ', $pharFile->sizeReadable(), PHP_EOL;
    $pharFile->delete();
}

$baseDir = dirname(__DIR__);
$baseDirOffset = strlen($baseDir) + 1;
$iterator = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($baseDir),
        function (SplFileInfo $file) use ($baseDirOffset) {
            // BLACKLIST: by filename
            $fileName = $file->getFilename();
            if (in_array($fileName, ['', '.', '..', 'composer.lock', 'phpunit.xml', 'phpunit.xml.dist'])) {
                return false;
            }

            // WHITELIST: by top level path (project root)
            $relativePath = substr($file->getPathname(), $baseDirOffset);
            if (preg_match('/^(data|src|composer\.json|console.php|VERSION)/', $relativePath) === 1) {
                return true;
            }

            // BLACKLIST: by top level path, by any file pattern
            if (
                preg_match('/^(bin|test|build|\..*|Jenkinsfile|LICENSE|Tests|php-|php_|phpunit)/', $fileName) === 1
                || preg_match('/\.(phar|md|json|lock)$/i', $fileName) === 1
            ) {
                return false;
            }

            return true;
        }
    )

);

$phar = new Phar($pharFile->path());
$phar->setMetadata(['version' => $version]);
$phar->buildFromIterator($iterator, $baseDir);
$phar->setStub($phar->createDefaultStub('console.php'));
$phar->compressFiles(Phar::BZ2);
$pharFile->reset();

echo 'File size: ', $pharFile->sizeReadable(), PHP_EOL;
