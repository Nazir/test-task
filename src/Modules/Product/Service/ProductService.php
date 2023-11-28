<?php

declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Exception as Except;
use App\Modules\Product\Entity as Prod;
use App\Model\DataList;
use App\Modules\Product\Dto\ProductDto;
use App\Modules\Product\Factory\ProductFactory;
use App\Modules\Product\Model\ProductList;
use App\Modules\Product\Repository as RepoProd;
use App\Service\BaseService;
use App\Service\Common\SerializerService;
use App\Service\Common\DbService;
use App\Service\Common\EntityService;

final class ProductService extends BaseService implements ProductServiceInterface
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly DbService $dbService,
        private readonly RepoProd\ProductRepository $repository,
        private readonly SerializerService $serializerService,
        private readonly ProductFactory $factory,
    ) {
    }

    /**
     * List
     */
    public function list(null|ProductList $list = null): DataList
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
    public function create(ProductDto $dto): Prod\Product
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
    public function read(int $id, bool $throw = true): null|Prod\Product
    {
        $obj = $this->repository->find($id);

        if (true === $throw && null === $obj) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: Prod\Product::class,
                id: $id,
                propertyName: 'product',
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
    public function update(int|Prod\Product $objOrObjId, ProductDto $dto, bool $throw = true): null|Prod\Product
    {
        $id = $objOrObjId instanceof Prod\Product ? $objOrObjId->getId() : $objOrObjId;
        $obj = $this->read(id: $id, throw: $throw);

        if (null === $obj) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: Prod\Product::class,
                id: $id,
                propertyName: 'product',
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
    public function delete(int|Prod\Product $objOrObjId, bool $throw = true): void
    {
        $id = $objOrObjId instanceof Prod\Product ? $objOrObjId->getId() : $objOrObjId;

        $obj = $this->read(id: $id, throw: $throw);

        if ($obj instanceof Prod\Product) {
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
        int|Prod\Product $objOrObjId,
        bool $throw = true,
    ): void {
        $id = $objOrObjId instanceof Prod\Product ? $objOrObjId->getId() : $objOrObjId;
        $obj = $this->read(id: $id, throw: $throw);

        if ($obj instanceof Prod\Product) {
            $obj->setDeleted(restore: true);
            $this->entityService->save($obj);
        }
    }
}
