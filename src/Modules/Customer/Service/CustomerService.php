<?php

declare(strict_types=1);

namespace App\Modules\Customer\Service;

use App\Exception as Except;
use App\Modules\Customer\Entity as Cust;
use App\Model\DataList;
use App\Modules\Customer\Dto\CustomerDto;
use App\Modules\Customer\Factory\CustomerFactory;
use App\Modules\Customer\Model\CustomerList;
use App\Modules\Customer\Repository as RepoCust;
use App\Service\BaseService;
use App\Service\Common\SerializerService;
use App\Service\Common\DbService;
use App\Service\Common\EntityService;

final class CustomerService extends BaseService implements CustomerServiceInterface
{
    public const IGNORED_ATTRIBUTES = ['cityId'];

    public function __construct(
        private readonly EntityService $entityService,
        private readonly DbService $dbService,
        private readonly RepoCust\CustomerRepository $repository,
        private readonly SerializerService $serializerService,
        private readonly CustomerFactory $factory,
    ) {
    }

    /**
     * List
     */
    public function list(null|CustomerList $list = null): DataList
    {
        return $this->repository->list(list: $list);
    }

    /**
     * Create
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function create(CustomerDto $dto): Cust\Customer
    {
        $obj = $this->factory->fromDto(dto: $dto);

        $this->entityService->save($obj);

        return $obj;
    }

    /**
     * Read
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function read(int $id, bool $throw = true): null|Cust\Customer
    {
        $obj = $this->repository->find($id);

        if (true === $throw && null === $obj) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: Cust\Customer::class,
                id: $id,
                propertyName: 'customer',
                code: 0, // TODO:
            );
        }

        return $obj;
    }

    /**
     * Update
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function update(int|Cust\Customer $objOrObjId, CustomerDto $dto, bool $throw = true): null|Cust\Customer
    {
        $id = $objOrObjId instanceof Cust\Customer ? $objOrObjId->getId() : $objOrObjId;
        $obj = $this->read(id: $id, throw: $throw);

        if (null === $obj) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: Cust\Customer::class,
                id: $id,
                propertyName: 'customer',
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
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function delete(int|Cust\Customer $objOrObjId, bool $throw = true): void
    {
        $id = $objOrObjId instanceof Cust\Customer ? $objOrObjId->getId() : $objOrObjId;

        $obj = $this->read(id: $id, throw: $throw);

        if ($obj instanceof Cust\Customer) {
            // $this->entityService->remove($obj);
            $obj->setDeleted();
            $this->entityService->save($obj);
        }
    }

    /**
     * Restore
     *
     * @param bool $throw Throw an exception
     *
     * @throws \App\Exception\ValidationException
     */
    public function restore(
        int|Cust\Customer $objOrObjId,
        bool $throw = true,
    ): void {
        $id = $objOrObjId instanceof Cust\Customer ? $objOrObjId->getId() : $objOrObjId;
        $obj = $this->read(id: $id, throw: $throw);

        if ($obj instanceof Cust\Customer) {
            $obj->setDeleted(restore: true);
            $this->entityService->save($obj);
        }
    }
}
