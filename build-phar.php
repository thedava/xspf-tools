<?php

if (file_exists('order-xspf.phar')) {
    unlink('order-xspf.phar');
}

$phar = new Phar('order-xspf.phar');
$phar->setMetadata(['version' => (float)trim(file_get_contents(__DIR__ . '/VERSION'))]);
$phar->buildFromDirectory(__DIR__, '/(src|tpl|vendor|order-xspf\.php)/');
$phar->setStub($phar->createDefaultStub('order-xspf.php'));
