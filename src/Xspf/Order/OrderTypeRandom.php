<?php

namespace Xspf\Order;

use Xspf\File;

class OrderTypeRandom extends AbstractOrderType
{
    /**
     * @param File $file
     */
    public function order(File $file)
    {
        $tracks = $file->getTracks();
        shuffle($tracks);
        $file->setTracks($tracks);
    }
}
