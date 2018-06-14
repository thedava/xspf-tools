<?php

namespace Xspf\File;

use Xspf\File\Type\AbstractFileType;
use Xspf\Track;

class File
{
    const BACKUP_SUFFIX = '.bak';

    /** @var string */
    protected $fileName;

    /** @var array|Track[] */
    protected $tracks = [];

    /** @var AbstractFileType */
    protected $fileType;

    /**
     * File constructor.
     *
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->fileType = AbstractFileType::factory($fileName);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return array|Track[]
     */
    public function getTracks()
    {
        return $this->tracks;
    }

    /**
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param array|Track[] $tracks
     *
     * @return $this
     */
    public function setTracks($tracks)
    {
        $this->tracks = $tracks;

        return $this;
    }

    /**
     * @return AbstractFileType
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param AbstractFileType $fileType
     *
     * @return $this
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * @return $this
     */
    public function load()
    {
        $data = file_get_contents($this->getFileName());
        $structure = $this->fileType->decode($data);

        $this->setTracks($structure->getTracks());

        return $this;
    }

    /**
     * @param bool $backup
     */
    public function save($backup = true)
    {
        // Create backup
        if ($backup && file_exists($this->fileName)) {
            if (file_exists($this->fileName . self::BACKUP_SUFFIX)) {
                unlink($this->fileName . self::BACKUP_SUFFIX);
            }
            copy($this->fileName, $this->fileName . self::BACKUP_SUFFIX);
        }

        $structure = new Structure();
        $structure->setTracks($this->getTracks());

        // Save new file
        file_put_contents($this->fileName, $this->fileType->encode($structure));
    }
}
