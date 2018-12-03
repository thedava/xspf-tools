<?php

namespace Xspf\Order;

use Xspf\Index\IndexModel;
use Xspf\Track;

abstract class AbstractOrderTypeSorting extends AbstractOrderType
{
    abstract protected function getSortingType();

    /**
     * @param array|Track[] $tracks
     *
     * @return array|Track[]
     */
    public function orderTracks(array $tracks)
    {
        $files = [];
        foreach ($tracks as $track) {
            $files[] = basename($track->getLocation());
        }
        array_multisort($files, $this->getSortingType(), $tracks);

        return $tracks;
    }

    public function orderIndex(IndexModel $indexModel)
    {
        $files = $indexModel->getFiles();
        array_multisort($files, $this->getSortingType(), $files);
        $indexModel->setFiles($files);
    }
}
