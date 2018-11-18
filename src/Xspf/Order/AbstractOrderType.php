<?php

namespace Xspf\Order;

use Xspf\File\File;
use Xspf\Index\IndexModel;
use Xspf\Track;

abstract class AbstractOrderType
{
    const TYPE_ASC = 'asc';
    const TYPE_DESC = 'desc';
    const TYPE_RANDOM = 'random';

    /**
     * @param File $file
     */
    public function orderFile(File $file)
    {
        $file->setTracks($this->orderTracks($file->getTracks()));
    }

    /**
     * @param array|Track[] $tracks
     *
     * @return array|Track[]
     */
    abstract public function orderTracks(array $tracks);

    /**
     * @param IndexModel $indexModel
     */
    abstract public function orderIndex(IndexModel $indexModel);

    /**
     * Returns all valid order types
     *
     * @return array
     */
    public static function getOrderTypes()
    {
        return [
            static::TYPE_ASC,
            static::TYPE_DESC,
            static::TYPE_RANDOM,
        ];
    }

    /**
     * @param string $orderType
     *
     * @return static
     *
     * @throws \Exception
     */
    public static function factory($orderType)
    {
        switch ($orderType) {
            case static::TYPE_ASC:
                return new OrderTypeAsc();

            case static::TYPE_DESC:
                return new OrderTypeDesc();

            case static::TYPE_RANDOM:
                return new OrderTypeRandom();

            default:
                throw new \Exception('Unexpected order type!');
        }
    }
}
