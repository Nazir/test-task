<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Modules\Order\Repository\OrderProductRepository;
use App\Modules\Product\Entity\Product;
use App\Serializer\SerializerDef;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Order - Product
 * Заявка - Товар
 */
#[ORM\Entity(repositoryClass: OrderProductRepository::class)]
#[ORM\Table(
    name: OrderDef::TBL_NAME_ORDER_PRODUCT,
    schema: OrderDef::DB_SCHEMA,
    options: ['comment' => 'Заявка - Товар'],
)]
class OrderProduct
{
    /**
     * Заявка ID
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'products')]
    #[ORM\JoinColumn(
        name: 'order_id',
        referencedColumnName: 'id',
        unique: false,
        nullable: true,
        options: ['comment' => 'Заявка ID'],
    )]
    #[Assert\NotNull()]
    // #[Groups(SerializerDef::DEFAULT_GROUPS)]
    // #[MaxDepth(1)]
    protected Order $order;

    /**
     * Товар ID
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(
        name: 'product_id',
        referencedColumnName: 'id',
        unique: false,
        nullable: true,
        options: ['comment' => 'Товар ID'],
    )]
    #[Assert\NotNull()]
    // #[Groups(SerializerDef::DEFAULT_GROUPS)]
    // #[MaxDepth(1)]
    protected Product $product;

    /**
     * Количество товара
     */
    #[ORM\Column(
        name: 'product_quantity',
        type: Types::INTEGER,
        unique: false,
        nullable: false,
        options: ['comment' => 'Количество товара'],
    )]
    #[Assert\NotNull()]
    #[Assert\Positive()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private int $productQuantity;

    public function __construct(
        Order $order,
        Product $product,
        int $productQuantity,
    ) {
        $this->order = $order;
        $this->product = $product;
        $this->productQuantity = $productQuantity;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setStatus(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function setProductQuantity(int $productQuantity): self
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    public function getProductQuantity(): int
    {
        return $this->productQuantity;
    }
}
