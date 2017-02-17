<?php

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__.'/../src/');
$files = glob('{*.php, **/*.php,**/*/*.php}', GLOB_BRACE);

// Create ASC ordered
call_user_func(function($files) {
    sort($files, SORT_ASC);
    $tracks = [];
    foreach ($files as $file) {
        $tracks[] = new \Xspf\Track(realpath($file));
    }
    (new \Xspf\File(__DIR__.'/../test/fixtures/asc.xspf'))
        ->setTracks($tracks)
        ->save(false);
}, $files);

// Create DESC ordered
call_user_func(function($files) {
    sort($files, SORT_DESC);
    $tracks = [];
    foreach ($files as $file) {
        $tracks[] = new \Xspf\Track(realpath($file));
    }
    (new \Xspf\File(__DIR__.'/../test/fixtures/desc.xspf'))
        ->setTracks($tracks)
        ->save(false);
}, $files);

// Create missing files
call_user_func(function($files) {
    $i = 0;
    $tracks = [];
    foreach ($files as $file) {
        $location = realpath($file);
        if ($i++ % 2 == 0) {
            $location .= '.missing';
        }
        $tracks[] = new \Xspf\Track($location);
    }
    (new \Xspf\File(__DIR__.'/../test/fixtures/missing.xspf'))
        ->setTracks($tracks)
        ->save(false);
}, $files);
