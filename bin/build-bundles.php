<?php

use Xspf\Utils;

require_once __DIR__ . '/../vendor/autoload.php';

// Validate that a xspf.phar exists and can be bundled
$buildBasePath = __DIR__ . '/../build';
$binary = $buildBasePath . '/xspf.phar';
if (!file_exists($binary)) {
    echo 'Cannot create bundles: binary ("', basename($binary), '") is missing!', PHP_EOL;

    return 1;
}

// Iterate through all bundles
$bundleBasePath = __DIR__ . '/../bundles';
$templateBundle = '_template';
$bundleConfig = 'bundle.json';
$comment = 'Bundled with xspf-tools version ' . Utils::getVersion() . ' at ' . (new DateTime())->format('c');
foreach (glob($bundleBasePath . '/*') as $bundleFolder) {
    // Skip template bundle and files
    if (!is_dir($bundleFolder) || ($bundleName = basename($bundleFolder)) === $templateBundle) {
        continue;
    }

    // Put all files from the bundle into a new zip archive (except for the bundle.json)
    echo 'Creating bundle "', $bundleName, '"...', PHP_EOL;
    $zip = new ZipArchive();
    $zip->open($buildBasePath . '/' . $bundleName . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFile($binary, basename($binary));
    foreach (glob($bundleFolder . '/*') as $file) {
        if (($fileName = basename($file)) === $bundleConfig) {
            continue;
        }

        $zip->addFile($file, $fileName);
    }

    // Enrich the README.md of the template bundle with everything from the bundle.json
    $readme = file_get_contents($bundleBasePath . '/' . $templateBundle . '/README.md');
    $config = json_decode(file_get_contents($bundleFolder . '/' . $bundleConfig), true);
    foreach ($config as $key => $value) {
        if (is_array($value)) {
            $list = '';
            foreach ($value as $v) {
                $list .= '* ' . $v . "\n";
            }

            $value = $list;
        }

        $count = 0;
        $readme = str_replace('%' . $key . '%', $value, $readme, $count);

        if ($count <= 0) {
            echo 'Key "', $key, '" not found in README.md!', PHP_EOL;

            return 4;
        }
    }
    $zip->addFromString('README.md', $readme);
    $zip->setArchiveComment($readme . "\n\n------------\n" . '@' . $comment);
    if ($zip->close()) {
        echo 'Bundle "', $bundleName, '" created!', PHP_EOL;
    } else {
        echo 'Failed to create bundle "', $bundleName, '"!', PHP_EOL;

        return 8;
    }
}
