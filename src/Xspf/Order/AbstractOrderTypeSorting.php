<?php

namespace Xspf\Order;

use Xspf\File\File;
use Xspf\Index\IndexModel;

abstract class AbstractOrderTypeSorting extends AbstractOrderType
{
    abstract protected function getSortingType();

    /**
     * @param File $file
     */
    public function orderFile(File $file)
    {
        $files = [];
        $tracks = $file->getTracks();

        foreach ($tracks as $track) {
            $files[] = basename($track->getLocation());
        }

        array_multisort($files, $this->getSortingType(), $tracks);
        $file->setTracks($tracks);
    }

    public function orderIndex(IndexModel $indexModel)
    {
        $files = $indexModel->getFiles();
        array_multisort($files, $this->getSortingType(), $files);
        $indexModel->setFiles($files);
    }
}
