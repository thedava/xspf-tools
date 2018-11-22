<?php

namespace Xspf\Index\FileHandler;

use Xspf\Index\IndexModelInterface;

interface FileHandlerInterface
{
    /**
     * @param IndexModelInterface $indexModel
     */
    public function setIndexModel(IndexModelInterface $indexModel);

    /**
     * @return bool
     */
    public function save();

    /**
     * @return \ArrayObject
     */
    public function load();
}
