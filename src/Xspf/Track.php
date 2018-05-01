<?php

namespace Xspf;

use getID3;

class Track
{
    /** @var getID3 */
    protected static $id3;

    /** @var string */
    protected $location;

    /** @var int|null */
    protected $duration = null;

    /**
     * @return getID3
     */
    public static function getId3()
    {
        if (!self::$id3) {
            self::$id3 = new \getID3();
        }

        return self::$id3;
    }

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

    public function update()
    {
        $result = self::getId3()->analyze($this->getLocation());

        $this->setDuration(isset($result['playtime_seconds']) ? (int)$result['playtime_seconds'] : $this->getDuration());

        return true;
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
