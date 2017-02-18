<?php

namespace Xspf;

class File
{
    const BACKUP_SUFFIX = '.bak';

    /** @var string */
    protected $fileName;

    /** @var array|Track[] */
    protected $tracks = [];

    /**
     * File constructor.
     *
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return array|Track[]
     */
    public function getTracks()
    {
        return $this->tracks;
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @param array|Track[] $tracks
     *
     * @return $this
     */
    public function setTracks($tracks)
    {
        $this->tracks = $tracks;
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function load()
    {
        $eMsg = 'Invalid or malformed xspf!';
        $xml = simplexml_load_file($this->fileName);

        if (!isset($xml->{'trackList'})) {
            throw new \Exception($eMsg . ' Field trackList is missing.');
        } elseif (!isset($xml->{'trackList'}->{'track'})) {
            throw new \Exception($eMsg . ' Field trackList.track is missing.');
        }

        $this->tracks = [];
        foreach ($xml->{'trackList'}->{'track'} as $track) {
            if (!isset($track->{'location'})) {
                $trackObj = new Track(LocationFilter::filter((string)$track));
            } else {
                $trackObj = new Track(LocationFilter::filter((string)$track->{'location'}));

                if (isset($track->{'duration'})) {
                    $trackObj->setDuration((int)(string)$track->{'duration'});
                }
            }

            $this->tracks[] = $trackObj;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function toXml()
    {
        $playlist = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><playlist></playlist>');
        $playlist->addAttribute('version', 1);
        $playlist->addAttribute('xmlns', 'http://xspf.org/ns/0/');

        $trackList = $playlist->addChild('trackList');
        foreach ($this->tracks as $track) {
            $track->toXml($trackList->addChild('track'));
        }

        $dom = dom_import_simplexml($playlist);
        $dom->ownerDocument->formatOutput = true;

        return $dom->ownerDocument->saveXML();
    }

    /**
     * @param bool $backup
     */
    public function save($backup = true)
    {
        // Create backup
        if ($backup && file_exists($this->fileName)) {
            if (file_exists($this->fileName . self::BACKUP_SUFFIX)) {
                unlink($this->fileName . self::BACKUP_SUFFIX);
            }
            copy($this->fileName, $this->fileName . self::BACKUP_SUFFIX);
        }

        // Save new file
        file_put_contents($this->fileName, $this->toXml());
    }
}
