<?php

namespace Xspf\Order;

use Xspf\File;
use Xspf\Index\IndexModel;

class OrderTypeRandom extends AbstractOrderType
{
    /**
     * @param File $file
     */
    public function orderFile(File $file)
    {
        $tracks = $file->getTracks();
        shuffle($tracks);
        $file->setTracks($tracks);
    }

    public function orderIndex(IndexModel $indexModel)
    {
        $files = $indexModel->getFiles();
        shuffle($files);
        $indexModel->setFiles($files);
    }
}
