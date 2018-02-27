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
            $location = str_replace(['/', '\\', $directorySeparator], $directorySeparator, substr($location, strlen($scheme)));

            // Do the opposite work of the FileUrlFilter
            $replaces = FileUrlFilter::getReplaces();
            $location = str_replace(array_values($replaces), array_keys($replaces), $location);

            $parts = explode($directorySeparator, $location);
            array_walk($parts, function (&$value) use ($replaces) {
                $value = utf8_decode(rawurldecode($value));
            }, $parts);

            $location = implode($directorySeparator, $parts);
        }

        // Windows path
        if (strpos($location, '\\') === 0) {
            $location = substr($location, 1);
        }

        return $location;
    }
}
