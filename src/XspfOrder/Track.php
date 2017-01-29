<?php

namespace XspfOrder;

class Track
{
    /** @var string */
    protected $location;

    /** @var int */
    protected $duration;

    /**
     * Track constructor.
     *
     * @param string $location
     * @param int    $duration
     */
    public function __construct($location, $duration)
    {
        $this->location = $location;
        $this->duration = $duration;
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
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }
}
