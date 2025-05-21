<?php

namespace Xspf\Index\FileHandler;

use Xspf\Utils;

class CompressedFileHandler extends AbstractYamlFileHandler
{
    /**
     * @return bool
     */
    public function save(bool $absolutePaths = false): bool
    {
        Utils::trackPerformance('Index', 'Saving compressed...');
        $handle = gzopen($this->indexModel->getIndexFile(), 'w9');
        gzwrite($handle, $this->encode($absolutePaths));
        gzclose($handle);
        Utils::trackPerformance('Index', 'Saving finished');

        return true;
    }

    /**
     * @return array
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
