<?php

namespace Xspf\Index;

class IndexModel
{
    const EXT_PLAIN = 'xd';
    const EXT_COMPRESSED = 'xdc';

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
        return $this->useCompression()
            ? $this->loadPlain()
            : $this->loadCompressed();
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    protected function loadPlain()
    {
        $this->clear();
        $lineCount = 0;
        $handle = fopen($this->indexFile, 'r');
        while (($line = fgets($handle)) !== false) {
            if (trim($line) === '') {
                continue;
            }

            $this->data[] = $this->decode($line, ++$lineCount);
        }
        fclose($handle);

        return $this;
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    protected function loadCompressed()
    {
        $this->clear();
        $lineCount = 0;
        $handle = gzopen($this->indexFile, 'rb');
        while (($line = gzgets($handle)) !== false) {
            if (trim($line) === '') {
                continue;
            }

            $this->data[] = $this->decode($line, ++$lineCount);
        }
        gzclose($handle);

        return $this;
    }

    /**
     * @param bool $append
     *
     * @return $this
     */
    public function save($append = false)
    {
        return $this->useCompression()
            ? $this->saveCompressed($append)
            : $this->savePlain($append);
    }

    /**
     * @param bool $append
     *
     * @return $this
     */
    protected function saveCompressed($append = false)
    {
        $this->sort();

        $handle = gzopen($this->indexFile, sprintf('%s' . 'b9', $append ? 'a' : 'w'));
        foreach ($this->data as $line) {
            gzwrite($handle, $this->encode($line));
        }
        gzclose($handle);

        return $this;
    }

    /**
     * @param bool $append
     *
     * @return $this
     */
    protected function savePlain($append = false)
    {
        $this->sort();

        $handle = fopen($this->indexFile, sprintf('%s' . 'b9', $append ? 'a' : 'w'));
        foreach ($this->data as $line) {
            fwrite($handle, $this->encode($line));
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

        if (strpos($file, $cwd) === 0) {
            $file = substr($file, strlen($cwd) + 1);
        }

        $this->data[] = [
            'file' => $file,
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

            if (!empty($file)) {
                yield $file;
            }
        }
    }

    /**
     * @return bool
     */
    protected function useCompression()
    {
        return !preg_match(sprintf('/\.%s/', self::EXT_PLAIN), $this->indexFile);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    protected function encode($data)
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES) . "\n";
    }

    /**
     * @param $line
     * @param $lineCount
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function decode($line, $lineCount)
    {
        $json = json_decode($line, true);
        if (!is_array($json) || !isset($json['file'])) {
//                throw new \Exception($line);
            throw new \Exception(vsprintf('Invalid or malformed data in index file %s on line %d', [
                basename($this->indexFile),
                $lineCount,
            ]));
        }

        return $json;
    }
}
