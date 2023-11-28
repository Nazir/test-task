<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Exception as Except;
use App\Modules\Order\Entity as OE;
use App\Model\DataList;
use App\Modules\Order\Dto\OrderDto;
use App\Modules\Order\Factory\OrderFactory;
use App\Modules\Order\Model\OrderList;
use App\Modules\Order\Repository as RepoOrder;
use App\Modules\Order\Repository\OrderStatusRepository;
use App\Service\BaseService;
use App\Service\Common\SerializerService;
use App\Service\Common\DbService;
use App\Service\Common\EntityService;
use App\Service\References\ReferencesDef;

/**
 * Order service
 * Сервис заявки
 *
 * При создании заявки проверяется, если город доставки клиента = городу клиента во всех товарах,
 * то заявка принимает статус “Ожидает отправки”, иначе - “Ожидает обработки”.
 * Такие заявки будут обрабатываться вручную менеджером и отправляться
 * ТК(Транспортная компания) после согласования с клиентом.
 * При этом в заявку будет добавляться информация о цене доставки.
 * Товары, находящиеся в том же городе, что и клиент, отправляются бесплатно.
 *
 */
final class OrderService extends BaseService implements OrderServiceInterface
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly DbService $dbService,
        private readonly RepoOrder\OrderRepository $repository,
        private readonly SerializerService $serializerService,
        private readonly OrderFactory $factory,
        private readonly OrderStatusRepository $orderStatusRepository,
    ) {
    }

    /**
     * List
     */
    public function list(null|OrderList $list = null): DataList
    {
        return $this->repository->list(list: $list);
    }

    /**
     * Create
     * Создание
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function create(OrderDto $dto): OE\Order
    {
        $obj = $this->factory->fromDto(dto: $dto);

        $customerCity = $obj->getCustomer()->getCity();

        /** @var null|OE\OrderStatus $orderStatusWP */
        $orderStatusWP = $this->orderStatusRepository->findByAlias(ReferencesDef::ORDER_STATUS_WAITING_TO_BE_PROCESSED);

        if (null === $orderStatusWP) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: OE\OrderStatus::class,
                id: ReferencesDef::ORDER_STATUS_WAITING_TO_BE_PROCESSED,
                propertyName: 'status',
                fieldName: 'alias',
                code: 0, // TODO:
            );
        }

        foreach ($obj->getProducts() as $orderProduct) {
            $productCity = $orderProduct->getProduct()->getStorage()->getCity();
            if ($productCity !== $customerCity) {
                $obj->setStatus($orderStatusWP);
                break;
            }
        }

        $this->entityService->save($obj);

        return $obj;
    }

    /**
     * Read
     * Чтение
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function read(int $id, bool $throw = true): null|OE\Order
    {
        $obj = $this->repository->find($id);

        if (true === $throw && null === $obj) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: OE\Order::class,
                id: $id,
                propertyName: 'order',
                code: 0, // TODO:
            );
        }

        return $obj;
    }

    /**
     * Update
     * Модификация
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function update(int|OE\Order $objOrObjId, OrderDto $dto, bool $throw = true): null|OE\Order
    {
        // TODO: Check role
        $id = $objOrObjId instanceof OE\Order ? $objOrObjId->getId() : $objOrObjId;
        $obj = $this->read(id: $id, throw: $throw);

        if (null === $obj) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: OE\Order::class,
                id: $id,
                propertyName: 'order',
                code: 0, // TODO:
            );
        }

        $obj = $this->factory->fromDto(dto: $dto);

        $obj->setDateUpdate();

        $this->entityService->save($obj);

        return $obj;
    }

    /**
     * Delete
     * Удаление
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function delete(int|OE\Order $objOrObjId, bool $throw = true): void
    {
        // TODO: Check role
        $id = $objOrObjId instanceof OE\Order ? $objOrObjId->getId() : $objOrObjId;

        $obj = $this->read(id: $id, throw: $throw);

        if ($obj instanceof OE\Order) {
            // $this->entityService->remove($obj);
            $obj->setDeleted();
            $this->entityService->save($obj);
        }
    }
}
