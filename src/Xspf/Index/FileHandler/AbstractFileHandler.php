<?php

namespace Xspf\Index\FileHandler;

use Xspf\Index\IndexModelInterface;
use Xspf\Utils;

abstract class AbstractFileHandler implements FileHandlerInterface
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
     * @return array
     *
     * @throws \Exception
     */
    final protected function getData()
    {
        $cwd = realpath(dirname(Utils::determinePath($this->indexModel->getIndexFile(), true)));
        $length = strlen($cwd);

        $data = new \ArrayObject();
        foreach ($this->indexModel->getData() as $file) {
            $file = Utils::determinePath($file, true);
            if (strpos($file, $cwd) === 0) {
                $file = mb_substr($file, $length + 1);
            }

            $data[] = $file;
        }

        return $data->getArrayCopy();
    }
}
