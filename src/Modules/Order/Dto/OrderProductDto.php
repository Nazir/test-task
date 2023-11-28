<?php

declare(strict_types=1);

namespace App\Modules\Order\Dto;

use App\Exception as Except;
use App\Model\Dto\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

use function intval;
use function is_numeric;

final class OrderProductDto implements DTOInterface
{
    // /**
    //  * Заявка ID
    //  */
    // #[Assert\NotNull()]
    // private int $orderId;

    /**
     * Товар ID
     */
    #[Assert\NotNull()]
    private int $productId;

    /**
     * Количество товара
     */
    #[Assert\NotNull()]
    #[Assert\Positive()]
    private int $productQuantity;

    public function __construct(
        // null|int|string $orderId,
        null|int|string $productId,
        null|int|string $productQuantity,
    ) {
        // $this->setOrderId($orderId);
        $this->setProductId($productId);
        $this->setProductQuantity($productQuantity);
    }

    // public function getOrderId(): int
    // {
    //     return $this->orderId;
    // }

    // public function setOrderId(null|int|string $orderId): self
    // {
    //     if (false === is_numeric($orderId)) {
    //         throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Заявка ID');
    //     }

    //     $this->orderId = intval($orderId);

    //     return $this;
    // }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(null|int|string $productId): self
    {
        if (false === is_numeric($productId)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Товар ID');
        }

        $this->productId = intval($productId);

        return $this;
    }

    public function setProductQuantity(null|int|string $productQuantity): self
    {
        if (false === is_numeric($productQuantity)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Количество товара');
        }

        $this->productQuantity = intval($productQuantity);

        return $this;
    }

    public function getProductQuantity(): int
    {
        return $this->productQuantity;
    }
}
