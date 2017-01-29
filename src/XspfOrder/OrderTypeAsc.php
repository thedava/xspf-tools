<?php

namespace XspfOrder;

class OrderTypeAsc extends AbstractOrderTypeSorting
{
    protected function getSortingType()
    {
        return SORT_ASC;
    }
}
