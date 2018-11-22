<?php

namespace Xspf\Index\FileHandler;

use Xspf\Utils;

class CompressedFileHandler extends AbstractYamlFileHandler
{
    /**
     * @return bool
     */
    public function save()
    {
        Utils::trackPerformance('Index', 'Saving compressed...');
        $handle = gzopen($this->indexModel->getIndexFile(), 'w9');
        gzwrite($handle, $this->encode());
        gzclose($handle);
        Utils::trackPerformance('Index', 'Saving finished');

        return true;
    }

    /**
     * @return \ArrayObject
     */
    public function load()
    {
        Utils::trackPerformance('Index', 'Loading compressed...');
        $handle = gzopen($this->indexModel->getIndexFile(), 'rb');
        $content = '';
        while (($line = gzgets($handle)) !== false) {
            $content .= $line;
        }
        gzclose($handle);
        $data = $this->decode($content);
        Utils::trackPerformance('Index', 'Loading finished');

        return $data;
    }
}
