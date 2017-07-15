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
     * @param bool $useFileUrl
     *
     * @return array
     */
    public function toArray($useFileUrl = false)
    {
        $result = [];
        $result['location'] = ($useFileUrl) ? $this->getFileUrl() : $this->getLocation();
        $this->getDuration() && $result['duration'] = (int)$this->getDuration();

        return $result;
    }

    /**
     * @param string $directorySeparator
     *
     * @return string
     */
    public function getFileUrl($directorySeparator = DIRECTORY_SEPARATOR)
    {
        $parts = explode($directorySeparator, $this->getLocation());
        list($first) = $parts;
        $parts = array_map('rawurlencode', $parts);

        // Check if windows
        if (preg_match('/^[A-Z]\:$/', $first)) {
            $parts[0] = '/' . $first;
        } elseif ($first == '' && isset($parts[1], $parts[2]) && $parts[1] == 'cygdrive' && preg_match('/^[a-z]$/', $parts[2])) {
            $parts[0] = '/' . strtoupper($parts[2]) . ':';
            unset($parts[1], $parts[2]);
        }

        return 'file://' . implode('/', $parts);
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
