<?php

namespace Xspf\Index;

use Xspf\Index\FileHandler\FileHandlerInterface;
use Xspf\Utils;

class IndexModel implements IndexModelInterface
{
    /** @var string */
    protected $indexFile;

    /** @var FileHandlerInterface */
    protected $fileHandler;

    /** @var array */
    protected $data;

    /**
     * @param string               $indexFile
     * @param FileHandlerInterface $fileHandler
     */
    public function __construct($indexFile, FileHandlerInterface $fileHandler)
    {
        $this->indexFile = $indexFile;
        $this->fileHandler = $fileHandler;
        $fileHandler->setIndexModel($this);
        $this->clear();
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Clear the index data from memory
     *
     * @return $this
     */
    public function clear()
    {
        $this->data = [];

        return $this;
    }

    /**
     * @param string $cwd
     * @param string $file
     * @param int    $line
     *
     * @return string
     *
     * @throws \Exception
     */
    private function loadSingleFile($cwd, $file, $line)
    {
        try {
            // Check for relative path
            return Utils::determinePath($cwd . DIRECTORY_SEPARATOR . $file, true);
        } catch (\Exception $e) {
            try {
                // Maybe path is absolute
                return Utils::determinePath($file, true);
            } catch (\Exception $e) {
                throw new \Exception('Could not locate file "' . $file . '" (#' . ($line + 1) . ')', 0, $e);
            }
        }
    }

    /**
     * Load data from index file into memory
     *
     * @param bool $force
     * @param bool $ignoreErrors
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function load($force = false, $ignoreErrors = false)
    {
        if ($force || count($this->data) <= 0) {
            $this->clear();
            $cwd = Utils::determinePath($this->getIndexFile(), true);
            foreach ($this->fileHandler->load() as $l => $file) {
                $path = null;
                $exception = null;
                foreach ([$file, utf8_encode($file), utf8_decode($file)] as $f) {
                    try {
                        $path = $this->loadSingleFile($cwd, $f, $l);
                    } catch (\Exception $exception) {
                    }
                }

                if ($path !== null) {
                    $this->addFile($path);
                } elseif (!$ignoreErrors) {
                    if ($exception !== null) {
                        throw $exception;
                    } else {
                        throw new \Exception('Unknown error while locating file "' . $file . '"');
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function save(bool $absolutePaths = false)
    {
        $this->sort();
        $this->fileHandler->save($absolutePaths);

        return $this;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        if (file_exists($this->indexFile)) {
            unlink($this->indexFile);
        }

        return $this;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function addFile($file)
    {
        $file = realpath($file);
        $this->data[] = $file;

        return $this;
    }

    /**
     * @param array $files
     *
     * @return $this
     */
    public function setFiles(array $files)
    {
        $this->clear();
        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * Sort the index
     *
     * @return $this
     */
    public function sort()
    {
        Utils::trackPerformance('Index', 'Sorting...');
        asort($this->data);
        Utils::trackPerformance('Index', 'Sorting finished');

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return string
     */
    public function getIndexFile()
    {
        return $this->indexFile;
    }

    /**
     * Return the files of the index
     *
     * @return string[]
     */
    public function getFiles()
    {
        return $this->getData();
    }
}
