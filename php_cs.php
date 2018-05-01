<?php

use DavaHome\PhpCsFixer;
use PhpCsFixer\Finder;

require_once __DIR__ . '/vendor/autoload.php';
$finder = Finder::create()
    ->in(__DIR__);

return (new PhpCsFixer($finder))
    ->getRuleSet()
    ->setCacheFile(__DIR__ . '/.php_cs.cache');
