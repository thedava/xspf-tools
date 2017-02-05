<?php

namespace Xspf;

class Utils
{
    /** @var null|float */
    private static $version = null;

    /**
     * @return float|null
     */
    public static function getVersion()
    {
        if (self::$version === null) {
            self::$version = (float)trim(file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'VERSION'));
        }

        return self::$version;
    }

    /**
     * Tries to determine the correct path for the given file
     *
     * @param string $fileName
     *
     * @return string
     * @throws \Exception
     */
    public static function determinePath($fileName)
    {
        // File exists in current context
        if (file_exists($fileName)) {
            return realpath($fileName);
        }

        // File exists in current working directory
        $ds = DIRECTORY_SEPARATOR;
        $dir = getcwd();
        if (file_exists($dir . $ds . $fileName)) {
            return $dir . $ds . $fileName;
        }

        // Current folder
        if (file_exists(__DIR__ . $ds . $fileName)) {
            return __DIR__ . $ds . $fileName;
        }

        // Phar folder
        $phar = \Phar::running(false);
        if ($phar !== '') {
            $dir = dirname($phar);
            // Same folder as phar archive
            if (file_exists($dir . $ds . $fileName)) {
                return $dir . $ds . $fileName;
            }
        }

        throw new \Exception('File could not be located! Please enter an absolute path.');
    }
}
