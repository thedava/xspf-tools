<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/AbstractCommandIntegrationTest.php';

define('XSPF_TEMP_DIR', __DIR__ . '/data');

define('XSPF_FIXTURE_ASC', __DIR__ . '/fixtures/asc.xspf');
define('XSPF_FIXTURE_DESC', __DIR__ . '/fixtures/desc.xspf');
define('XSPF_FIXTURE_MISSING', __DIR__ . '/fixtures/missing.xspf');
define('XSPF_FIXTURE_INVALID', __DIR__ . '/fixtures/invalid.xspf');

// Clear data folder onStart and onFinish
$clearDataFolder = function (bool $clearAllFiles) {
    foreach (glob(XSPF_TEMP_DIR . '/*') as $item) {
        if ($clearAllFiles || strpos($item, '.xml') === false) {
            unlink($item);
        }
    }
};
$clearDataFolder(true);
register_shutdown_function(function () use ($clearDataFolder) {
    $clearDataFolder(false);
});
