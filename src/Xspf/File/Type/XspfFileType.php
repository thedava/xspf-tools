<?php

namespace Xspf\File\Type;

use Xspf\File\Structure;
use Xspf\Filter\FileUrlFilter;
use Xspf\Filter\LocalFileFilter;
use Xspf\Track;

class XspfFileType extends AbstractFileType
{
    /**
     * Parse XSPF into structure
     *
     * @param string $data
     *
     * @return Structure
     * @throws \Exception
     */
    protected function toStructure($data)
    {
        $structure = new Structure();

        $eMsg = 'Invalid or malformed xspf!';
        $xml = simplexml_load_string($data);

        if (!isset($xml->{'trackList'})) {
            throw new \Exception($eMsg . ' Field trackList is missing.');
        } elseif (!isset($xml->{'trackList'}->{'track'})) {
            throw new \Exception($eMsg . ' Field trackList.track is missing.');
        }

        $tracks = [];
        foreach ($xml->{'trackList'}->{'track'} as $track) {
            if (!isset($track->{'location'})) {
                $trackObj = new Track(LocalFileFilter::filter((string)$track));
            } else {
                $trackObj = new Track(LocalFileFilter::filter((string)$track->{'location'}));

                if (isset($track->{'duration'})) {
                    $trackObj->setDuration((int)(string)$track->{'duration'});
                }
            }

            $tracks[] = $trackObj;
        }

        $structure->setTracks($tracks);

        return $structure;
    }

    /**
     * @param Structure $structure
     *
     * @return string
     */
    protected function fromStructure(Structure $structure)
    {
        $playlist = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><playlist></playlist>');
        $playlist->addAttribute('version', 1);
        $playlist->addAttribute('xmlns', 'http://xspf.org/ns/0/');

        $trackList = $playlist->addChild('trackList');
        foreach ($structure->getTracks() as $track) {
            $eTrack = $trackList->addChild('track');

            foreach ($track->toArray() as $key => $value) {
                if ($key === 'location') {
                    $value = FileUrlFilter::filter($value);
                }
                $eTrack->addChild($key, htmlspecialchars($value));
            }
        }

        $dom = dom_import_simplexml($playlist);
        $dom->ownerDocument->formatOutput = true;

        return $dom->ownerDocument->saveXML();
    }
}
