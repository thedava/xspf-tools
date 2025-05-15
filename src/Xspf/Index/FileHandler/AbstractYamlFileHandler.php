<?php

namespace Xspf\Index\FileHandler;

use Symfony\Component\Yaml\Yaml;

abstract class AbstractYamlFileHandler extends AbstractFileHandler
{
    /**
     * @return string
     */
    public function encode(bool $absolutePaths = false): string
    {
        return Yaml::dump($this->getData($absolutePaths));
    }

    /**
     * @param string $content
     *
     * @return \ArrayObject
     */
    public function decode($content)
    {
        return new \ArrayObject(Yaml::parse($content));
    }
}
