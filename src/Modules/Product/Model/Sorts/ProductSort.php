<?php

declare(strict_types=1);

namespace App\Modules\Product\Model\Sorts;

use App\Entity\EntityDef;
use App\Model\Sorts\Interfaces as CommonInterfaces;
use App\Model\Sorts\Sort;
use App\Model\Sorts\Traits as CommonTraits;
use Symfony\Component\Validator\Constraints as Assert;

class ProductSort extends Sort implements CommonInterfaces\SortInterface
{
    use CommonTraits\SortTrait;

    #[Assert\NotBlank()]
    #[Assert\Choice([
        EntityDef::COL_ID,
        'name',
    ])]
    private string $property = 'name';
}
