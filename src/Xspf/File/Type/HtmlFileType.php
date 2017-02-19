<?php

namespace Xspf\File\Type;

use Xspf\File\Structure;

class HtmlFileType extends AbstractFileType
{
    protected function toStructure($data)
    {
        throw new \Exception('Decoding from HTML is not supported!');
    }

    protected function fromStructure(Structure $structure)
    {
        $t = "\n";
        $result = [];
        $result[] = '<!DOCTYPE html>';
        $result[] = '<html>';
        $result[] = $t . '<head>';
        $result[] = $t . $t . '<title>Xspf Tools</title>';
        $result[] = $t . '</head>';
        $result[] = $t . '<body>';
        $result[] = $t . $t . '<div class="trackList">';

        foreach ($structure->getTracks() as $track) {
            $result[] = $t . $t . $t . '<div class="track">';
            foreach ($track->toArray() as $key => $value) {
                $result[] = $t . $t . $t . $t . '<div class="' . $key . '">' . $value . '</div>';
            }
            $result[] = $t . $t . $t . '</div>';
        }

        $result[] = $t . $t . '</div>';
        $result[] = $t . '</body>';
        $result[] = '</html>';

        return implode(PHP_EOL, $result);
    }
}
