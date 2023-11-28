<?php

declare(strict_types=1);

namespace App\Modules\Customer\Factory;

use App\Entity\References\City;
use App\Modules\Customer\Dto\CustomerDto;
use App\Modules\Customer\Entity\Customer;
use App\Repository\References\CityRepository;
use App\Service\Common\EntityService;

class CustomerFactory
{
    public function __construct(
        private readonly EntityService $entityService,
        private readonly CityRepository $cityRepository,
    ) {
    }

    public function fromDto(CustomerDto $dto): Customer
    {
        $city = $this->cityRepository->find($dto->getCityId());

        if (null === $city) {
            throw $this->entityService->createObjectNotFountException(
                objOrClass: City::class,
                id: $dto->getCityId(),
                propertyName: 'cityId',
                code: 0, // TODO:
            );
        }

        $customer = new Customer(
            name: $dto->getName(),
            phone: $dto->getPhone(),
            email: $dto->getEmail(),
            city:  $city,
            street: $dto->getStreet(),
            houseNumber: $dto->getHouseNumber(),
            apartmentNumber: $dto->getApartmentNumber(),
        );

        return $customer;
    }
}
