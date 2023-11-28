<?php

declare(strict_types=1);

namespace App\Modules\Product\Entity;

use App\Entity\DbDef;
use App\Entity\References as Ref;
use App\Entity\Traits as CommonTraits;
use App\Modules\Product\Repository\ProductRepository;
use App\Serializer\SerializerDef;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 * Товар
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(
    name: ProductDef::TBL_NAME_PRODUCT,
    schema: ProductDef::DB_SCHEMA,
    options: ['comment' => 'Товар'],
)]
#[ORM\UniqueConstraint(name: DbDef::PREFIX_UNIQ . ProductDef::TBL_NAME_PRODUCT, columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
class Product implements Stringable
{
    use CommonTraits\IdTrait;
    use CommonTraits\TimestampsTrait;
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
     * Цена
     */
    #[ORM\Column(
        name: 'price',
        type: Types::DECIMAL,
        unique: false,
        nullable: false,
        options: ['comment' => 'Цена'],
    )]
    #[Assert\NotNull()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private float $price;

    /**
     * Кол-во доступное для заказа
     */
    #[ORM\Column(
        name: 'quantity_available_for_order',
        type: Types::INTEGER,
        unique: false,
        nullable: false,
        options: ['comment' => 'Кол-во доступное для заказа'],
    )]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[Assert\NotNull()]
    protected int $quantityAvailableForOrder;

    /**
     * Склад ID
     */
    #[ORM\ManyToOne(targetEntity: Ref\Storage::class)]
    #[ORM\JoinColumn(
        name: 'storage_id',
        referencedColumnName: 'id',
        unique: false,
        nullable: true,
        options: ['comment' => 'Склад ID'],
    )]
    #[Assert\NotNull()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[MaxDepth(1)]
    protected Ref\Storage $storage;

    public function __construct(
        string $name,
        float $price,
        int $quantityAvailableForOrder,
        Ref\Storage $storage,
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->quantityAvailableForOrder = $quantityAvailableForOrder;
        $this->storage = $storage;
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

    public function setPrice(float $price): self
    {
        $this->price = round($price, 2);

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setQuantityAvailableForOrder(int $quantityAvailableForOrder): self
    {
        $this->quantityAvailableForOrder = $quantityAvailableForOrder;

        return $this;
    }

    public function getQuantityAvailableForOrder(): int
    {
        return $this->quantityAvailableForOrder;
    }

    public function getStorage(): Ref\Storage
    {
        return $this->storage;
    }

    public function setStorage(Ref\Storage $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
