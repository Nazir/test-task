<?php

declare(strict_types=1);

namespace App\Modules\Product\Dto;

use App\Exception as Except;
use App\Model\Dto\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

use function intval;
use function is_numeric;
use function round;

class ProductDto implements DTOInterface
{
    /**
     * Название
     */
    #[Assert\NotBlank()]
    private string $name;

    /**
     * Цена
     */
    #[Assert\NotNull()]
    private float $price;

    /**
     * Кол-во доступное для заказа
     */
    #[Assert\NotNull()]
    private int $quantityAvailableForOrder;

    /**
     * Склад ID
     */
    #[Assert\NotNull()]
    private int $storageId;

    public function __construct(
        string $name,
        float $price,
        int $quantityAvailableForOrder,
        int $storageId,
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->quantityAvailableForOrder = $quantityAvailableForOrder;
        $this->storageId = $storageId;
    }

    public function setName(null|string $name): self
    {
        if (empty($name)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Название');
        }

        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPrice(null|float $price): self
    {
        if (null === $price) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Цена');
        }

        $this->price = round($price, 2);

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setQuantityAvailableForOrder(null|int|string $quantityAvailableForOrder): self
    {
        if (false === is_numeric($quantityAvailableForOrder)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле  Кол-во доступное для заказа');
        }

        $this->quantityAvailableForOrder = (int) $quantityAvailableForOrder;

        return $this;
    }

    public function getQuantityAvailableForOrder(): int
    {
        return $this->quantityAvailableForOrder;
    }


    public function getStorageId(): int
    {
        return $this->storageId;
    }

    public function setStorageId(null|string|int $storageId): self
    {
        if (false === is_numeric($storageId)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Склад ID');
        }

        $this->storageId = intval($storageId);

        return $this;
    }
}
