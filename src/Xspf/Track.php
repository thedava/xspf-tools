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
     * @return array
     */
    public function toArray()
    {
        $result = [];
        $result['location'] = $this->getLocation();
        $this->getDuration() && $result['duration'] = (int)$this->getDuration();

        return $result;
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
