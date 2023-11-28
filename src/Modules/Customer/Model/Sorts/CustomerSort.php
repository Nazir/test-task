<?php

declare(strict_types=1);

namespace App\Modules\Customer\Model\Sorts;

use App\Entity\EntityDef;
use App\Model\Sorts\Interfaces as CommonInterfaces;
use App\Model\Sorts\Sort;
use App\Model\Sorts\Traits as CommonTraits;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerSort extends Sort implements CommonInterfaces\SortInterface
{
    use CommonTraits\SortTrait;

    #[Assert\NotBlank()]
    #[Assert\Choice([
        EntityDef::COL_ID,
        'name',
        'phone',
        'email',
        'deliveryAddressCity',
        'deliveryAddressStreet',
        'deliveryAddressHouseNumber',
        'deliveryAddressApartmentNumber',
    ])]
    private string $property = 'name';
}
