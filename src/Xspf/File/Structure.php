<?php

namespace Xspf\File;

use Xspf\Track;

class Structure
{
    /** @var array|Track[] */
    protected $tracks = [];

    /**
     * @return array|Track[]
     */
    public function getTracks()
    {
        return $this->tracks;
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
}
