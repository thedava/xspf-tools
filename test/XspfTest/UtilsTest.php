<?php

namespace XspfTest;

use PHPUnit\Framework\TestCase;
use Xspf\Utils;

class UtilsTest extends TestCase
{
    public function testGetVersion()
    {
        $version = Utils::getVersion();

        $this->assertGreaterThan(0, $version);
    }
}
