<?php

namespace XspfTest\File\Type;

use Exception;
use PHPUnit\Framework\TestCase;
use Xspf\File\Structure;
use Xspf\File\Type\XspfFileType;
use Xspf\Track;
use Xspf\XspfSchemeValidator;

class XspfFileTypeTest extends TestCase
{
    public function fileNameDataProvider()
    {
        return [
            ['test.mp3'],
            ['t(es)t.mp3'],
            ['Runde Räder sind toll.mp4'], // Uml Ä
            ['Bömische Dörfer.flv'], // Uml Ö
        ];
    }

    public function testEncode()
    {
        $this->markTestSkipped('Skipped due to external api call');

        $structure = new Structure();
        $structure->setTracks([
            (new Track('foo/bar.mp4')),
            (new Track('foo/baz.mkv'))->setDuration(1337),
        ]);

        $result = (new XspfFileType())->encode($structure);
        $isValid = XspfSchemeValidator::isValid($result);

        if ($isValid === null) {
            $this->markTestIncomplete('Invalid validation result');
        }

        $this->assertTrue($isValid);
    }

    /**
     * @dataProvider fileNameDataProvider
     *
     * @param string $fileName
     *
     * @throws Exception
     */
    public function testFilter($fileName)
    {
        $structure = new Structure();
        $structure->setTracks([new Track($fileName)]);

        $fileType = new XspfFileType();

        // De and Encode multiple times
        for ($i = 0; $i < 5; $i++) {
            $content = $fileType->encode($structure);
            $structure = $fileType->decode($content);

            $tracks = $structure->getTracks();
            $this->assertIsArray($tracks);
            $this->assertCount(1, $tracks);

            $track = $tracks[0];
            $this->assertInstanceOf(Track::class, $track);
            $this->assertEquals($fileName, $track->getLocation());
        }
    }
}
