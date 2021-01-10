<?php

namespace Xspf\Utils;

class LocalFile
{
    /** @var string */
    protected $originalPath;

    /** @var string */
    protected $confirmedPath;

    /** @var array */
    protected $fileMetaData;

    public function __construct($file)
    {
        $this->originalPath = $file;
        $this->reset();
    }

    private function reset()
    {
        $this->fileMetaData = [];

        // Determine confirmed path
        $this->confirmedPath = null;
        if (file_exists($this->originalPath)) {
            $this->confirmedPath = $this->originalPath;
        } elseif (($path = realpath($this->originalPath)) !== false && file_exists($path)) {
            $this->confirmedPath = $path;
        }
    }

    /**
     * @return bool
     */
    public function exists()
    {
        if (!array_key_exists('exists', $this->fileMetaData)) {
            $this->fileMetaData['exists'] = ($this->confirmedPath !== null && file_exists($this->confirmedPath));
        }

        return $this->fileMetaData['exists'];
    }

    /**
     * @return int
     */
    public function size()
    {
        if (!array_key_exists('size', $this->fileMetaData)) {
            $this->fileMetaData['size'] = ($this->exists()) ? filesize($this->confirmedPath) : false;

            if ($this->fileMetaData['size'] === false) {
                $this->fileMetaData['size'] = -1;
            }
        }

        return $this->fileMetaData['size'];
    }

    public function mtime()
    {
        if (!array_key_exists('mtime', $this->fileMetaData)) {
            $this->fileMetaData['mtime'] = ($this->exists()) ? filemtime($this->confirmedPath) : null;

            if ($this->fileMetaData['mtime'] === false) {
                $this->fileMetaData['mtime'] = null;
            }
        }

        return $this->fileMetaData['mtime'];
    }

    public function delete()
    {
        $result = false;
        if ($this->exists()) {
            $result = unlink($this->confirmedPath);

            $this->reset();
        }

        return $result;
    }

    /**
     * @param mixed $content
     *
     * @return bool
     */
    public function put($content)
    {
        if (file_put_contents($this->originalPath, $content) !== false) {
            $this->reset();

            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function read()
    {
        if ($this->exists() && ($result = file_get_contents($this->confirmedPath)) !== false) {
            return $result;
        }

        return null;
    }
}
