<?php

namespace Xspf;

class LocationFilter
{
    public static function filter($location)
    {
        // file-"protocol"
        if (strpos($location, 'file://') === 0) {
            $location = urldecode($location);
            $location = str_replace(['/', '\\', DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, substr($location, 7));
        }

        // Windows path
        if (strpos($location, '\\') === 0) {
            $location = substr($location, 1);
        }

        return $location;
    }
}
