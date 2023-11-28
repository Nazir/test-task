<?php

declare(strict_types=1);

namespace App\Modules\Customer\Model;

use App\Model\Page;
use App\Model\Filters\Traits as FiltersTraits;
use App\Model\Interfaces as CommonInterfaces;

final class CustomerList implements CommonInterfaces\ListInterface
{
    use FiltersTraits\FilterTrait;
    use FiltersTraits\ModeTrait;

    public function __construct(
        public readonly Page|null $page = null,
        public readonly Sorts\CustomerSort|null $sort = null,
    ) {
    }
}
