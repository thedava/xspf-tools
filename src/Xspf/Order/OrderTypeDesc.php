<?php

namespace Xspf\Order;

class OrderTypeDesc extends AbstractOrderTypeSorting
{
    protected function getSortingType()
    {
        return SORT_DESC;
    }
}
