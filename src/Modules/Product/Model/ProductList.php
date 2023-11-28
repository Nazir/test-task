<?php

declare(strict_types=1);

namespace App\Modules\Product\Model;

use App\Model\Page;
use App\Model\Filters\Traits as FiltersTraits;
use App\Model\Interfaces as CommonInterfaces;

final class ProductList implements CommonInterfaces\ListInterface
{
    use FiltersTraits\FilterTrait;
    use FiltersTraits\ModeTrait;

    public function __construct(
        public readonly Page|null $page = null,
        public readonly Sorts\ProductSort|null $sort = null,
    ) {
    }
}
