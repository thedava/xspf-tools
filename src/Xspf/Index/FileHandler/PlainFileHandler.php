<?php

namespace Xspf\Index\FileHandler;

class PlainFileHandler extends AbstractYamlFileHandler
{
    /**
     * @return bool
     */
    public function save(bool $absolutePaths = false): bool
    {
        return file_put_contents($this->indexModel->getIndexFile(), $this->encode($absolutePaths)) !== false;
    }

    /**
     * @return \ArrayObject
     */
    public function load()
    {
        return $this->decode(file_get_contents($this->indexModel->getIndexFile()));
    }
}
