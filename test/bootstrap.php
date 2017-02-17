<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('XSPF_TEMP_DIR', __DIR__ . '/data');

define('XSPF_FIXTURE_ASC', __DIR__ . '/fixtures/asc.xspf');
define('XSPF_FIXTURE_DESC', __DIR__ . '/fixtures/desc.xspf');
define('XSPF_FIXTURE_MISSING', __DIR__ . '/fixtures/missing.xspf');
define('XSPF_FIXTURE_INVALID', __DIR__ . '/fixtures/invalid.xspf');

// Clear data folder
foreach (glob(XSPF_TEMP_DIR . '/*') as $item) {
    unlink($item);
}
