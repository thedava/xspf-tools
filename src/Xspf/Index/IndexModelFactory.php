<?php

namespace Xspf\Index;

use Xspf\Index\FileHandler\CompressedFileHandler;
use Xspf\Index\FileHandler\FileHandlerInterface;
use Xspf\Index\FileHandler\PlainFileHandler;
use Xspf\Utils;

class IndexModelFactory
{
    /** @var bool */
    protected static $useMemoryDefault = false;

    /** @var array|IndexModel[] */
    protected static $memory = [];

    /**
     * @param $default
     */
    public static function setUseMemoryDefault($default)
    {
        self::$useMemoryDefault = (bool)$default;
    }

    /**
     * @param string $indexModelFile
     *
     * @return string
     */
    protected static function getFileExtension($indexModelFile)
    {
        return (mb_strlen($indexModelFile) >= 3 && mb_substr($indexModelFile, -2) === IndexModelInterface::EXT_PLAIN)
            ? IndexModelInterface::EXT_PLAIN
            : IndexModelInterface::EXT_COMPRESSED;
    }

    /**
     * @param string $indexModelFile
     *
     * @return FileHandlerInterface
     */
    protected static function determineFileHandler($indexModelFile)
    {
        switch (self::getFileExtension($indexModelFile)) {
            case IndexModelInterface::EXT_PLAIN:
                return new PlainFileHandler();

            case IndexModelInterface::EXT_COMPRESSED:
            default:
                return new CompressedFileHandler();
        }
    }

    /**
     * @param string $indexModelFile
     * @param bool   $useMemory
     *
     * @return IndexModel
     */
    public static function factory($indexModelFile, $useMemory = null)
    {
        if ($useMemory === null) {
            $useMemory = self::$useMemoryDefault;
        }

        if ($useMemory && isset(self::$memory[$indexModelFile])) {
            Utils::trackPerformance('IndexFactory', 'Loading IndexModel from memory');

            return self::$memory[$indexModelFile];
        }

        Utils::trackPerformance('IndexFactory', 'Creating new indexModel');
        $indexModel = new IndexModel($indexModelFile, self::determineFileHandler($indexModelFile));

        return ($useMemory)
            ? self::$memory[$indexModelFile] = $indexModel
            : $indexModel;
    }
}
