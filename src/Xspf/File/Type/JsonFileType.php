<?php

namespace Xspf\File\Type;

use Xspf\File\Structure;

class JsonFileType extends AbstractFileType
{
    protected function toStructure($data)
    {
//        $result = json_decode($data, true);
//
//        $structure
    }

    protected function fromStructure(Structure $structure)
    {
        // TODO: Implement fromStructure() method.
    }
}
