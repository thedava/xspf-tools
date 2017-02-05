<?php

namespace Xspf\Order;

class OrderTypeAsc extends AbstractOrderTypeSorting
{
    protected function getSortingType()
    {
        return SORT_ASC;
    }
}
