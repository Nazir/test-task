<?php

declare(strict_types=1);

namespace App\Modules\Order\Dto;

use App\Exception as Except;
use App\Model\Dto\DTOInterface;
use App\Service\Common\DateTimeService;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

use function intval;
use function is_numeric;
use function is_string;
use function round;

final class OrderDto implements DTOInterface
{
    /**
     * Клиент ID
     */
    #[Assert\NotNull()]
    private int $customerId;

    /**
     * Дата создания
     */
    private DateTimeInterface $date;

    /**
     * Статус ID
     */
    #[Assert\NotNull()]
    private int $statusId;

    /**
     * Внешний трек-номер (ТК)
     */
    private null|string $tk = null;

    /**
     * Цена доставки
     */
    private null|float $deliveryPrice = null;

    /**
     * @var OrderProductDto[] $products Товары
     */
    private array $products;

    public function __construct(
        int $customerId,
        int $statusId,
        null|string $tk,
        null|float $deliveryPrice,
        OrderProductDto ...$products,
    ) {
        $this->customerId = $customerId;
        $this->statusId = $statusId;
        $this->products = $products;
        $this->tk = $tk;
        if (null !== $deliveryPrice) {
            $this->deliveryPrice = round($deliveryPrice, 2);
        }

        $this->date = new DateTimeImmutable();
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(null|string|int $customerId): self
    {
        if (false === is_numeric($customerId)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Клиент ID');
        }

        $this->customerId = intval($customerId);

        return $this;
    }

    public function getStatusId(): int
    {
        return $this->statusId;
    }

    public function setStatusId(int|string $statusId): self
    {
        if (false === is_numeric($statusId)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Статус ID');
        }

        $this->statusId = intval($statusId);

        return $this;
    }

    /**
     * @param OrderProductDto[] $products
     */
    public function setProducts(array $products): self
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @return OrderProductDto[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function setDate(null|string|DateTimeInterface $date): self
    {
        if (is_string($date)) {
            $date = DateTimeService::createDateTimeFromString(dateTimeString: $date);
        }

        if (null === $date) {
            $this->date = new DateTimeImmutable();
        } else {
            if ($date instanceof DateTimeInterface) {
                $this->date = $date;
            }
        }

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
}
