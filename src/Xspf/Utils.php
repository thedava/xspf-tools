<?php

namespace Xspf;

use DateTime;
use Exception;
use Phar;

class Utils
{
    const PERFORMANCE_TRACKING_ENABLED = false;

    /**
     * @return string
     */
    public static function getVersionFile()
    {
        return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'VERSION';
    }

    /**
     * @return array
     */
    public static function getComposerJson()
    {
        return json_decode(file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'composer.json'), true);
    }

    /**
     * @return float|null
     */
    public static function getVersion()
    {
        static $version = null;
        if ($version === null) {
            $version = trim(file_get_contents(self::getVersionFile()));
        }

        return $version;
    }

    /**
     * @return bool
     */
    public static function isPhar()
    {
        static $isPhar = null;
        if ($isPhar === null) {
            $isPhar = stripos(__DIR__, 'phar://') === 0;
        }

        return $isPhar;
    }

    /**
     * @param string|null $path
     *
     * @return string
     */
    public static function setDirectory($path = null)
    {
        static $directory = null;
        if ($directory === null) {
            $directory = (self::isPhar())
                ? dirname(Phar::running(false))
                : (string)$path;
        }

        return $directory;
    }

    /**
     * @return string
     */
    public static function getDirectory()
    {
        return self::setDirectory();
    }

    /**
     * @param array $path
     *
     * @return string
     */
    public static function buildPath(array $path)
    {
        return self::getDirectory()
            . DIRECTORY_SEPARATOR
            . implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Tries to determine the correct path for the given file
     *
     * @param string $fileName
     * @param bool   $required
     *
     * @return string
     *
     * @throws Exception
     */
    public static function determinePath($fileName, $required = false)
    {
        // File exists in phar/project root
        $path = self::buildPath([$fileName]);
        if (file_exists($path)) {
            return $path;
        }

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

        // Abort now if the file is required
        if ($required) {
            throw new Exception('File could not be located! Please enter an absolute path.');
        }

        return $fileName;
    }

    /**
     * @param string $name
     * @param string $description
     */
    public static function trackPerformance($name, $description)
    {
        if (self::PERFORMANCE_TRACKING_ENABLED) {
            file_put_contents(self::buildPath(['performance.log']), vsprintf('[%s] <%s> %s' . PHP_EOL, [
                DateTime::createFromFormat('U.u', microtime(true))->format('Y-m-d H:i:s.u'),
                (string)$name,
                (string)$description,
            ]), FILE_APPEND);
        }
    }
}
