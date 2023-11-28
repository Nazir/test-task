<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Entity\Traits as CommonTraits;
use App\Modules\Customer\Entity\Customer;
use App\Modules\Order\Repository\OrderRepository;
use App\Serializer\SerializerDef;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Order
 * Заявка
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(
    name: OrderDef::TBL_NAME_ORDER,
    schema: OrderDef::DB_SCHEMA,
    options: ['comment' => 'Заявка'],
)]
#[ORM\HasLifecycleCallbacks]
final class Order
{
    use CommonTraits\IdTrait;
    use CommonTraits\TimestampsTrait;
    use CommonTraits\AuxiliaryTrait;
    use CommonTraits\DeletedTrait;

    /**
     * Клиент ID
     */
    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(
        name: 'customer_id',
        referencedColumnName: 'id',
        unique: false,
        nullable: true,
        options: ['comment' => 'Клиент ID'],
    )]
    #[Assert\NotNull()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[MaxDepth(1)]
    protected Customer $customer;

    /**
     * Дата создания
     */
    #[ORM\Column(
        name: 'date',
        type: Types::DATE_MUTABLE,
        unique: false,
        nullable: false,
        options: ['comment' => 'Дата создания'],
    )]
    #[Groups(SerializerDef::PREVIEW_GROUPS)]
    #[Context(
        normalizationContext: SerializerDef::API_DATE_TIME_NORMALIZATION_CONTEXT,
        denormalizationContext: SerializerDef::API_DATE_TIME_DENORMALIZATION_CONTEXT,
    )]
    protected DateTimeInterface $date;

    /**
     * Статус ID
     */
    #[ORM\ManyToOne(targetEntity: OrderStatus::class)]
    #[ORM\JoinColumn(
        name: 'status_id',
        referencedColumnName: 'id',
        unique: false,
        nullable: true,
        options: ['comment' => 'Статус ID'],
    )]
    #[Assert\NotNull()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[MaxDepth(1)]
    protected OrderStatus $status;

    /**
     * Внешний трек-номер (ТК)
     */
    #[ORM\Column(
        name: 'tk',
        type: Types::TEXT,
        unique: false,
        nullable: true,
        options: ['comment' => 'Внешний трек-номер (ТК)'],
    )]
    #[Assert\NotBlank()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private null|string $tk = null;

    /**
     * Цена доставки
     */
    #[ORM\Column(
        name: 'delivery_price',
        type: Types::DECIMAL,
        unique: false,
        nullable: true,
        options: ['comment' => 'Цена доставки'],
    )]
    #[Assert\NotNull()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private null|float $deliveryPrice = null;

    /**
     * @var Collection<int, OrderProduct> $products Товары
     */
    #[ORM\OneToMany(
        mappedBy: 'order',
        targetEntity: OrderProduct::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[MaxDepth(1)]
    protected Collection $products;

    public function __construct(
        Customer $customer,
        null|DateTimeInterface $date,
        OrderStatus $status,
        null|string $tk = null,
        null|float $deliveryPrice = null,
    ) {
        $this->customer = $customer;
        $this->date = $date ?? new DateTimeImmutable();
        $this->status = $status;
        $this->tk = $tk;
        if (null !== $deliveryPrice) {
            $this->deliveryPrice = round($deliveryPrice, 2);
        }

        $this->products = new ArrayCollection();
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setTk(null|string $tk): self
    {
        $this->tk = $tk;

        return $this;
    }

    public function getTk(): null|string
    {
        return $this->tk;
    }

    public function setDeliveryPrice(null|float $deliveryPrice): self
    {
        if (null !== $deliveryPrice) {
            $this->deliveryPrice = round($deliveryPrice, 2);
        } else {
            $this->deliveryPrice = null;
        }

        return $this;
    }

    public function getDeliveryPrice(): null|float
    {
        return $this->deliveryPrice;
    }

    /**
     * @param Collection<int, OrderProduct> $products
     */
    public function setProducts(Collection $products): self
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function existsProduct(OrderProduct $product): bool
    {
        return $this->products->contains($product);
    }

    public function addProduct(OrderProduct $product): void
    {
        $this->products->add($product);
    }

    public function removeProduct(OrderProduct $product): void
    {
        $this->products->removeElement($product);
    }
}
