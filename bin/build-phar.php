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

$phar = new Phar($pharFile);
$phar->setMetadata(['version' => $version]);
$phar->buildFromIterator($iterator, $baseDir);
$phar->setStub($phar->createDefaultStub('console.php'));
$phar->compressFiles(Phar::GZ);

echo 'File size: ', round(filesize($pharFile) / 1024, 2), ' kB', PHP_EOL;
