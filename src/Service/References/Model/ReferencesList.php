<?php

namespace App\Service\References\Model;

use App\Model\Filters\Traits as FiltersTraits;

final class ReferencesList
{
    use FiltersTraits\FilterTrait;

    public function __construct(
        public readonly null|Sorts\ReferencesSort $sort = null,
    ) {
    }
}
