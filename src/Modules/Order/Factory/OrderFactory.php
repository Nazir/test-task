<?php

declare(strict_types=1);

namespace App\Modules\Order\Factory;

use App\Modules\Customer\Entity\Customer;
use App\Modules\Customer\Repository\CustomerRepository;
use App\Modules\Order\Dto\OrderDto;
use App\Modules\Order\Entity\Order;
use App\Modules\Order\Entity\OrderProduct;
use App\Modules\Order\Entity\OrderStatus;
use App\Modules\Order\Repository\OrderStatusRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Service\Common\EntityService;

final class OrderFactory
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly CustomerRepository $customerRepository,
        private readonly OrderStatusRepository $orderStatusRepository,
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function fromDto(OrderDto $dto): Order
    {
        $customer = $this->customerRepository->find($dto->getCustomerId());

        if (null === $customer) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: Customer::class,
                id: $dto->getCustomerId(),
                propertyName: 'customerId',
                code: 0, // TODO:
            );
        }

        $orderStatus = $this->orderStatusRepository->find($dto->getStatusId());

        if (null === $orderStatus) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: OrderStatus::class,
                id: $dto->getStatusId(),
                propertyName: 'statusId',
                code: 0, // TODO:
            );
        }

        $order = new Order(
            customer: $customer,
            date: $dto->getDate(),
            status: $orderStatus,
            tk: $dto->getTk(),
            deliveryPrice: $dto->getDeliveryPrice(),
        );

        foreach ($dto->getProducts() as $orderProductId) {
            $product = $this->productRepository->find($orderProductId->getProductId());
            if (null === $product) {
                throw $this->entityService->createObjectNotFountException(
                    objOrClass: OrderStatus::class,
                    id: $orderProductId->getProductId(),
                    propertyName: 'products.productId',
                    code: 0, // TODO:
                );
            }

            $orderProduct = new OrderProduct(
                order: $order,
                product: $product,
                productQuantity: $orderProductId->getProductQuantity(),
            );
            $order->addProduct($orderProduct);
        }

        return $order;
    }
}
