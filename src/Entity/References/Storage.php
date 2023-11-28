<?php

declare(strict_types=1);

namespace App\Entity\References;

use App\Entity\DbDef;
use App\Entity\References as Ref;
use App\Entity\Traits as CommonTraits;
use App\Repository\References\StorageRepository;
use App\Serializer\SerializerDef;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Storage
 * Склад
 */
#[ORM\Entity(repositoryClass: StorageRepository::class)]
#[ORM\Table(
    name: ReferencesDef::TBL_NAME_STORAGE,
    schema: ReferencesDef::DB_SCHEMA,
    options: ['comment' => 'Склад'],
)]
#[ORM\UniqueConstraint(name: DbDef::PREFIX_UNIQ . ReferencesDef::TBL_NAME_STORAGE, columns: ['name'])]
class Storage implements Interfaces\ReferenceInterface
{
    use CommonTraits\IdTrait;
    // use CommonTraits\NameTrait;
    use CommonTraits\DeletedTrait;

    /**
     * Название
     */
    #[ORM\Column(
        name: 'name',
        type: Types::TEXT,
        unique: false,
        nullable: false,
        options: ['comment' => 'Название'],
    )]
    #[Assert\NotBlank()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private string $name;

    /**
     * Город ID
     */
    #[ORM\ManyToOne(targetEntity: Ref\City::class)]
    #[ORM\JoinColumn(
        name: 'city_id',
        referencedColumnName: 'id',
        unique: false,
        nullable: true,
        options: ['comment' => 'Город ID'],
    )]
    #[Assert\NotNull()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    protected Ref\City $city;

    public function __construct(
        string $name,
        City $city,
    ) {
        $this->name = $name;
        $this->city = $city;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): Ref\City
    {
        return $this->city;
    }

    public function setCity(Ref\City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
