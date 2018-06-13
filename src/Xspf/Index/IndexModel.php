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
     *
     * @throws \Exception
     */
    public function load()
    {
        $this->clear();

        $lineCount = 0;
        $handle = fopen($this->indexFile, 'r');
        while (($line = fgets($handle)) !== false) {
            $lineCount++;

            $json = json_decode($line, true);
            if (!is_array($json) || !isset($json['file'])) {
                throw new \Exception(vsprintf('Invalid or malformed data in index file %s on line %d', [
                    basename($this->indexFile),
                    $lineCount,
                ]));
            }

            $this->data[] = $json;
        }
        fclose($handle);

        return $this;
    }

    /**
     * @param bool $append
     *
     * @return $this
     */
    public function save($append = false)
    {
        $this->sort();

        $handle = fopen($this->indexFile, $append ? 'a' : 'w');
        foreach ($this->data as $line) {
            fwrite($handle, json_encode($line, JSON_UNESCAPED_SLASHES) . "\n");
        }
        fclose($handle);

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
