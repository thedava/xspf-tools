<?php

namespace Xspf\Index;

class IndexModel
{
    /** @var string */
    protected $indexFile;

    /** @var \ArrayObject */
    protected $data;

    /**
     * @param string $indexFile
     */
    public function __construct($indexFile)
    {
        $this->indexFile = $indexFile;

        $this->clear();
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
     */
    public function load()
    {
        $this->clear();

        $handle = fopen($this->indexFile, 'r');
        while (($line = fgetcsv($handle)) !== false) {
            $this->data[] = [
                'file' => $line[0],
                'md5'  => $line[1],
            ];
        }
        fclose($handle);

        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        $this->sort();

        $handle = fopen($this->indexFile, 'w');
        foreach ($this->data as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

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

        $md5 = md5_file($file);

        if (strpos($file, $cwd) === 0) {
            $file = substr($file, strlen($cwd) + 1);
        }

        $this->data[] = [
            'file' => $file,
            'md5'  => $md5,
        ];

        return $this;
    }

    /**
     * Sort the index
     *
     * @return $this
     */
    public function sort()
    {
        $this->data->asort();

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
        foreach ($this->data as $data) {
            $file = realpath($data['file']);

            if (!empty($file) && md5_file($file) == $data['md5']) {
                yield $file;
            }
        }
    }
}
