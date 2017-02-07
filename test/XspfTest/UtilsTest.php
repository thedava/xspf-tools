<?php

namespace XspfTest;


use Xspf\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetVersion()
    {
        $version = Utils::getVersion();

        $this->assertGreaterThan(0, $version);
    }
}
