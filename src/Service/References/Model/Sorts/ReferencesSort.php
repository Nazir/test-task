<?php

namespace App\Service\References\Model\Sorts;

use App\Entity\EntityDef;
use App\Model\Sorts\Interfaces as CommonInterfaces;
use App\Model\Sorts\Traits as CommonTraits;
use Symfony\Component\Validator\Constraints as Assert;

class ReferencesSort implements CommonInterfaces\SortInterface
{
    use CommonTraits\SortTrait;
    use CommonTraits\PropertyTrait;

    #[Assert\NotBlank()]
    #[Assert\Choice([EntityDef::COL_ID, EntityDef::COL_NAME])]
    private string $property = EntityDef::COL_NAME;
}
