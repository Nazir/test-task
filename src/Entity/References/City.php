<?php

namespace App\Entity\References;

use App\Entity\DbDef;
use App\Repository\References\CityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * City
 * Город
 */
#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table(
    name: ReferencesDef::TBL_NAME_CITY,
    schema: ReferencesDef::DB_SCHEMA,
    options: ['comment' => 'Город'],
)]
#[ORM\UniqueConstraint(name: DbDef::PREFIX_UNIQ . ReferencesDef::TBL_NAME_CITY, columns: ['name'])]
class City implements Interfaces\ReferenceInterface
{
    use Traits\BaseReferenceTrait;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
