<?php

namespace Xspf\Filter;

class FileUrlFilter
{
    private static $replace = [
        '%28' => '(',
        '%29' => ')',
    ];

    /**
     * @return array
     */
    public static function getReplaces()
    {
        return self::$replace;
    }

    /**
     * @param string $location
     * @param string $directorySeparator
     *
     * @return string
     */
    public static function filter($location, $directorySeparator = DIRECTORY_SEPARATOR)
    {
        $parts = explode($directorySeparator, $location);
        list($first) = $parts;
        array_walk($parts, function (&$value) {
//            $value = rawurlencode(utf8_encode($value));
            $value = rawurlencode($value);
        }, $parts);

        // Check if windows
        if (preg_match('/^[A-Z]\:$/', $first)) {
            $parts[0] = '/' . $first;
        } elseif ($first == '' && isset($parts[1], $parts[2]) && $parts[1] == 'cygdrive' && preg_match('/^[a-z]$/', $parts[2])) {
            $parts[0] = '/' . strtoupper($parts[2]) . ':';
            unset($parts[1], $parts[2]);
        }

        $file = 'file://' . implode('/', $parts);

        // Apply replaces
        return str_replace(array_keys(self::$replace), array_values(self::$replace), $file);
    }
}
