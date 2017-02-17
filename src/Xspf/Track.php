<?php

namespace Xspf;

class Track
{
    /** @var string */
    protected $location;

    /** @var int|null */
    protected $duration = null;

    /**
     * Track constructor.
     *
     * @param string $location
     */
    public function __construct($location)
    {
        $this->location = $location;
    }

    /**
     * @param \SimpleXMLElement $track
     */
    public function toXml(\SimpleXMLElement $track)
    {
        $track->addChild('location', htmlspecialchars($this->getLocation()));
        $this->getDuration() && $track->addChild('duration', (int)$this->getDuration());
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }
}
