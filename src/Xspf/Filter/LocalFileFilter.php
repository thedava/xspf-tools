<?php

namespace Xspf\Filter;

class LocalFileFilter
{
    /**
     * @param string $location
     * @param string $directorySeparator
     *
     * @return string
     */
    public static function filter($location, $directorySeparator = DIRECTORY_SEPARATOR)
    {
        // file-"protocol"
        $scheme = 'file://';
        if (strpos($location, $scheme) === 0) {
            $location = urldecode($location);
            $location = str_replace(['/', '\\', $directorySeparator], $directorySeparator, substr($location, strlen($scheme)));
        }

        // Windows path
        if (strpos($location, '\\') === 0) {
            $location = substr($location, 1);
        }

        return $location;
    }
}
