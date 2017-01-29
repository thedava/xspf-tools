<?php

namespace XspfOrder;

class File
{
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
                throw new \Exception($eMsg . ' Field trackList.track.location is missing.');
            } elseif (!isset($track->{'duration'})) {
                throw new \Exception($eMsg . ' Field trackList.track.duration is missing.');
            }

            $this->tracks[] = new Track((string)$track->{'location'}, (int)(string)$track->{'duration'});
        }
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    protected function loadTemplate($templateName)
    {
        $template = file_get_contents(__DIR__ . '/../../tpl/' . $templateName . '.xml');

        // Trim whitespaces
        $template = rtrim($template);
        $template = str_replace('    ', "\t", $template);

        // Force CRLF
        $template = str_replace("\r\n", "\n", $template);
        $template = str_replace("\n", "\r\n", $template);

        return $template;
    }

    /**
     * @param string $location
     *
     * @return mixed
     */
    protected function encodeLocation($location)
    {
//        $blackList = ['\''];
//        $length = strlen($location);
//
//        for ($i = 0; $i < $length; $i++) {
//            if (in_array($location[$i], $blackList)) {
//                $location[$i] = '&#' . ord($location[$i]) . ';';
//            }
//        }

        return htmlspecialchars($location);
    }

    /**
     * @return string
     */
    protected function createContent()
    {
        $vlcId = 0;

        $tracks = [];
        $extensions = [];

        $tplLayout = $this->loadTemplate('layout');
        $tplTrack = $this->loadTemplate('track');
        $tplExtension = $this->loadTemplate('extension');

        foreach ($this->tracks as $track) {
            // Add track
            $tracks[] = str_replace([
                '<!-- REPLACE:LOCATION -->',
                '<!-- REPLACE:DURATION -->',
                '<!-- REPLACE:VLC_ID -->',
            ], [
                $this->encodeLocation($track->getLocation()),
                $track->getDuration(),
                $vlcId,
            ], $tplTrack);

            // Add extension
            $extensions[] = str_replace([
                '<!-- REPLACE:VLC_ID -->',
            ], [
                $vlcId,
            ], $tplExtension);

            $vlcId++;
        }

        // Create full xml file
        return str_replace([
            '<!-- REPLACE:TITLE -->',
            '<!-- REPLACE:TRACKLIST -->',
            '<!-- REPLACE:EXTENSION -->',
        ], [
            'Tracklist',
            implode("\r\n", $tracks),
            implode("\r\n", $extensions),
        ], $tplLayout) . "\r\n";
    }

    public function save()
    {
        // Create backup
        if (file_exists($this->fileName . '.bak')) {
            unlink($this->fileName . '.bak');
        }
        copy($this->fileName, $this->fileName . '.bak');

        // Save new file
        file_put_contents($this->fileName, $this->createContent());
    }
}
