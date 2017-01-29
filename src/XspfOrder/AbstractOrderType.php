<?php

namespace XspfOrder;

abstract class AbstractOrderType
{
    const TYPE_ASC = 'asc';
    const TYPE_DESC = 'desc';
    const TYPE_RANDOM = 'random';

    /**
     * @param File $file
     */
    abstract public function order(File $file);

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
