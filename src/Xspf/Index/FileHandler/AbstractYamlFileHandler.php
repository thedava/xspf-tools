<?php

namespace Xspf\Index\FileHandler;

use Symfony\Component\Yaml\Yaml;
use Xspf\Index\IndexModelInterface;

abstract class AbstractYamlFileHandler implements FileHandlerInterface
{
    /**
     * @var IndexModelInterface
     */
    protected $indexModel;

    /**
     * @param IndexModelInterface $indexModel
     */
    public function setIndexModel(IndexModelInterface $indexModel)
    {
        $this->indexModel = $indexModel;
    }

    /**
     * @return string
     */
    public function encode()
    {
        return Yaml::dump($this->indexModel->getData()->getArrayCopy());
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
