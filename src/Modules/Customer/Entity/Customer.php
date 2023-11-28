<?php

declare(strict_types=1);

namespace App\Modules\Customer\Entity;

use App\Entity\DbDef;
use App\Entity\References as Ref;
use App\Entity\Traits as CommonTraits;
use App\Modules\Customer\Repository\CustomerRepository;
use App\Serializer\SerializerDef;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 * Клиент (покупатель)
 */
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(
    name: CustomerDef::TBL_NAME_CUSTOMER,
    schema: CustomerDef::DB_SCHEMA,
    options: ['comment' => 'Клиент (покупатель)'],
)]
#[ORM\UniqueConstraint(name: DbDef::PREFIX_UNIQ . CustomerDef::TBL_NAME_CUSTOMER . '_phone', columns: ['phone'])]
#[ORM\HasLifecycleCallbacks]
class Customer implements Stringable
{
    use CommonTraits\IdTrait;
    use CommonTraits\TimestampsTrait;
    use CommonTraits\DeletedTrait;

    /**
     * ФИО
     */
    #[ORM\Column(
        name: 'name',
        type: Types::TEXT,
        unique: false,
        nullable: false,
        options: ['comment' => 'ФИО'],
    )]
    #[Assert\NotBlank()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private string $name;

    /**
     * Номер телефона
     */
    #[ORM\Column(
        name: 'phone',
        type: Types::TEXT,
        unique: false,
        nullable: false,
        options: ['comment' => 'Номер телефона'],
    )]
    #[Assert\NotBlank()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private string $phone;

    /**
     * Email
     */
    #[ORM\Column(
        name: 'email',
        type: Types::TEXT,
        unique: false,
        nullable: false,
        options: ['comment' => 'Email'],
    )]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[Assert\NotBlank()]
    protected string $email;

    /**
     * Адрес доставки - Город ID
     */
    #[ORM\ManyToOne(targetEntity: Ref\City::class)]
    #[ORM\JoinColumn(
        name: 'city_id',
        referencedColumnName: 'id',
        unique: false,
        nullable: true,
        options: ['comment' => 'Адрес доставки - Город ID'],
    )]
    #[Assert\NotNull()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    protected Ref\City $city;

    /**
     * Адрес доставки - Улица
     */
    #[ORM\Column(
        name: 'street',
        type: Types::TEXT,
        unique: false,
        nullable: false,
        options: ['comment' => 'Адрес доставки - Улица'],
    )]
    #[Assert\NotBlank()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    protected string $street;

    /**
     * Адрес доставки - Номер дома
     */
    #[ORM\Column(
        name: 'house_number',
        type: Types::TEXT,
        unique: false,
        nullable: false,
        options: ['comment' => 'Адрес доставки - Номер дома'],
    )]
    #[Assert\NotBlank()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    protected string $houseNumber;

    /**
     * Адрес доставки - Квартира
     */
    #[ORM\Column(
        name: 'apartment_number',
        type: Types::TEXT,
        unique: false,
        nullable: true,
        options: ['comment' => 'Адрес доставки - Квартира'],
    )]
    #[Assert\NotBlank(allowNull: true)]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    protected null|string $apartmentNumber = null;

    public function __construct(
        string $name,
        string $phone,
        string $email,
        Ref\City $city,
        string $street,
        string $houseNumber,
        null|string $apartmentNumber = null,
    ) {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->city = $city;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->apartmentNumber = $apartmentNumber;
    }

    // public function unsetId(): self
    // {
    //     unset($this->id);

    //     return $this;
    // }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): self
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getApartmentNumber(): null|string
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(null|string $apartmentNumber): self
    {
        $this->apartmentNumber = $apartmentNumber;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
