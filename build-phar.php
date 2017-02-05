<?php

require_once 'vendor/autoload.php';

$pharFile = 'xspf.phar';
$includePattern = '/(VERSION|src|tpl|vendor|xspf\.php)/';
$version = \Xspf\Utils::getVersion();

if (file_exists($pharFile)) {
    unlink($pharFile);
}

$phar = new Phar($pharFile);
$phar->setMetadata(['version' => $version]);
$phar->buildFromDirectory(__DIR__, $includePattern);
$phar->setStub($phar->createDefaultStub('xspf.php'));
