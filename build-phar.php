<?php

if (file_exists('order-xspf.phar')) {
    unlink('order-xspf.phar');
}

$phar = new Phar('order-xspf.phar');
$phar->buildFromDirectory(__DIR__, '/(src|tpl|vendor|order-xspf\.php)/');
$phar->setStub($phar->createDefaultStub('order-xspf.php'));
