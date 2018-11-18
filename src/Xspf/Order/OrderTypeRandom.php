<?php

namespace Xspf\Order;

use Xspf\Index\IndexModel;
use Xspf\Track;

class OrderTypeRandom extends AbstractOrderType
{
    /**
     * @param array|Track[] $tracks
     *
     * @return array|Track[]
     */
    public function orderTracks(array $tracks)
    {
        shuffle($tracks);

        return $tracks;
    }

    public function orderIndex(IndexModel $indexModel)
    {
        $files = iterator_to_array($indexModel->getFiles());
        shuffle($files);
        $indexModel->setFiles($files);
    }
}
