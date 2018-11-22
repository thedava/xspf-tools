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

    /** @var \ArrayObject */
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
     * @return \ArrayObject
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
        $this->data = new \ArrayObject();

        return $this;
    }

    /**
     * Load data from index file into memory
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function load()
    {
        $this->clear();
        $this->data = $this->fileHandler->load();

        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        $this->sort();
        $this->fileHandler->save();

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
        $cwd = realpath(getcwd());
        $file = realpath($file);

        if (strpos($file, $cwd) === 0) {
            $file = substr($file, strlen($cwd) + 1);
        }

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
        $this->data->asort();
        Utils::trackPerformance('Index', 'Sorting finished');

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->data->count();
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
     * @return \Generator|string[]
     */
    public function getFiles()
    {
        foreach ($this->data as $file) {
            $file = realpath($file);

            if (!empty($file)) {
                yield $file;
            }
        }
    }
}
