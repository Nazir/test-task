<?php

declare(strict_types=1);

namespace App\Modules\Customer\Dto;

use App\Exception as Except;
use App\Model\Dto\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

use function intval;
use function is_numeric;

class CustomerDto implements DTOInterface
{
    /**
     * ФИО
     */
    #[Assert\NotBlank()]
    private string $name;

    /**
     * Номер телефона
     */
    #[Assert\NotBlank()]
    private string $phone;

    /**
     * Email
     */
    #[Assert\NotBlank()]
    private string $email;

    /**
     * Адрес доставки - Город ID
     */
    #[Assert\NotNull()]
    private int $cityId;

    /**
     * Адрес доставки - Улица
     */
    #[Assert\NotBlank()]
    private string $street;

    /**
     * Адрес доставки - Номер дома
     */
    #[Assert\NotBlank()]
    private string $houseNumber;

    /**
     * Адрес доставки - Квартира
     */
    #[Assert\NotBlank(allowNull: true)]
    private null|string $apartmentNumber = null;

    public function __construct(
        string $name,
        string $phone,
        string $email,
        int $cityId,
        string $street,
        string $houseNumber,
        null|string $apartmentNumber = null,
    ) {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->cityId = $cityId;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->apartmentNumber = $apartmentNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(null|string $name): self
    {
        if (empty($name)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Название');
        }

        $this->name = $name;

        return $this;
    }

    public function getPhone(): string
    {
        if (empty($this->phone)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Номер телефона');
        }

        return $this->phone;
    }

    public function setPhone(null|string $phone): self
    {
        if (empty($phone)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Номер телефона');
        }

        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): string
    {
        if (empty($this->email)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Email');
        }

        return $this->email;
    }

    public function setEmail(null|string $email): self
    {
        if (empty($email)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Email');
        }

        $this->email = $email;

        return $this;
    }

    public function getCityId(): int
    {
        return $this->cityId;
    }

    public function setCityId(null|string|int $cityId): self
    {
        if (false === is_numeric($cityId)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Город ID');
        }

        $this->cityId = intval($cityId);

        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(null|string $street): self
    {
        if (empty($street)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Улица');
        }

        $this->street = $street;

        return $this;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(null|string $houseNumber): self
    {
        if (empty($houseNumber)) {
            throw new Except\ValidationException('Ошибка параметра. Не заполнено поле Номер дома');
        }

        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getApartmentNumber(): null|string
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(null|string $apartmentNumber): self
    {
        $this->apartmentNumber = $apartmentNumber;

        return $this;
    }
}
