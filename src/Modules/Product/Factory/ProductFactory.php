<?php

declare(strict_types=1);

namespace App\Modules\Product\Factory;

use App\Entity\References\Storage;
use App\Modules\Product\Dto\ProductDto;
use App\Modules\Product\Entity\Product;
use App\Repository\References\StorageRepository;
use App\Service\Common\EntityService;

class ProductFactory
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly StorageRepository $storageRepository,
    ) {
    }

    public function fromDto(ProductDto $dto): Product
    {
        $storage = $this->storageRepository->find($dto->getStorageId());

        if (null === $storage) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: Storage::class,
                id: $dto->getStorageId(),
                propertyName: 'storageId',
                code: 0, // TODO:
            );
        }

        $product = new Product(
            name: $dto->getName(),
            price: $dto->getPrice(),
            quantityAvailableForOrder: $dto->getQuantityAvailableForOrder(),
            storage: $storage,
        );

        return $product;
    }
}
