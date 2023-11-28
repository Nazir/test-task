<?php

namespace App\Model\Sorts;

use App\Entity\EntityDef;
use Symfony\Component\Validator\Constraints as Assert;

final class DefaultSort extends Sort implements Interfaces\SortInterface
{
    use Traits\SortTrait;

    #[Assert\NotBlank()]
    #[Assert\Choice([EntityDef::COL_ID, EntityDef::COL_DATE_CREATE, EntityDef::COL_DATE_UPDATE])]
    private string $property = EntityDef::COL_ID;
}
