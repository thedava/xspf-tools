<?php

namespace XspfTest;


use Xspf\File;
use Xspf\Track;
use Xspf\XspfSchemeValidator;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testToXml()
    {
        $file = new File('php://memory');
        $file->setTracks([
            new Track(__FILE__),
        ]);
        $xml = $file->toXml();

        $this->assertTrue(XspfSchemeValidator::isValid($xml));
    }
}
