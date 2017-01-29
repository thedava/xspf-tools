<?php

namespace XspfOrder;

abstract class AbstractOrderTypeSorting extends AbstractOrderType
{
    abstract protected function getSortingType();

    /**
     * @param File $file
     */
    public function order(File $file)
    {
        $files = [];
        $tracks = $file->getTracks();

        foreach ($tracks as $track) {
            $files[] = basename($track->getLocation());
        }

        array_multisort($files, $this->getSortingType(), $tracks);
        $file->setTracks($tracks);
    }
}
