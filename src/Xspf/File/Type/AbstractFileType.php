<?php

namespace Xspf\File\Type;

use Xspf\File\Structure;

abstract class AbstractFileType
{
    /**
     * Convert the given fileType specific $data into an array
     *
     * @param mixed $data
     *
     * @return Structure
     */
    abstract protected function toStructure($data);

    /**
     * Convert the given $data-array into a fileType specific string
     *
     * @param Structure $structure
     *
     * @return mixed
     */
    abstract protected function fromStructure(Structure $structure);

    /**
     * @param Structure $structure
     *
     * @return mixed
     */
    public final function encode(Structure $structure)
    {
        return $this->fromStructure($structure);
    }

    /**
     * @param mixed $data
     *
     * @return Structure
     * @throws \Exception
     */
    public final function decode($data)
    {
        $result = $this->toStructure($data);

        if (!$result instanceof Structure) {
            throw new \Exception('The given fileType result is not a Structure!');
        }

        return $result;
    }

    /**
     * @param string $fileName
     *
     * @return self
     */
    public static function factory($fileName)
    {
        list($ext) = array_reverse(explode('.', $fileName));

        switch (strtolower($ext)) {
            case 'html':
            case 'phtml':
                return new HtmlFileType();

//            case 'php':
//                return new PhpFileType();

            case 'xspf':
            case 'xml':
            default:
                return new XspfFileType();
        }
    }
}
