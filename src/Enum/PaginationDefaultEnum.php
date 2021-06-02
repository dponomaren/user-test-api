<?php

namespace App\Enum;

class PaginationDefaultEnum extends Enum
{
    const PAGE               = 1;
    const ITEMS_PER_PAGE     = 25;
    const MIN_ITEMS_PER_PAGE = 1;
    const MAX_ITEMS_PER_PAGE = 50;
}