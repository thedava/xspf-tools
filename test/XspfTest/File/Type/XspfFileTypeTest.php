<?php

use Xspf\XspfSchemeValidator;

class XspfFileTypeTest extends PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $structure = new \Xspf\File\Structure();
        $structure->setTracks([
            (new \Xspf\Track('foo/bar.mp4')),
            (new \Xspf\Track('foo/baz.mkv'))->setDuration(1337),
        ]);

        $result = (new \Xspf\File\Type\XspfFileType())->encode($structure);
        $isValid = XspfSchemeValidator::isValid($result);

        if ($isValid === null) {
            $this->markTestIncomplete('Invalid validation result');
        }

        $this->assertTrue($isValid);
    }
}
