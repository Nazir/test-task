<?php

namespace App\Entity\Traits;

use App\Serializer\SerializerDef;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait DateUpdateTrait
{
    /**
     * Date of record update in the database
     */
    #[ORM\Column(
        name: '_date_update',
        type: Types::DATETIME_MUTABLE,
        unique: false,
        nullable: true,
        options: ['comment' => 'Date of record update in the database'],
    )]
    #[Assert\DateTime]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[Context(
        normalizationContext: SerializerDef::API_DATE_TIME_NORMALIZATION_CONTEXT,
        denormalizationContext: SerializerDef::API_DATE_TIME_DENORMALIZATION_CONTEXT,
    )]
    protected null|DateTimeInterface $dateUpdate = null;

    public function getDateUpdate(): null|DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(null|DateTimeInterface $dateUpdate = null): self
    {
        $this->dateUpdate = $dateUpdate ?? new DateTimeImmutable();

        return $this;
    }

    #[ORM\PostUpdate]
    public function setDateUpdateValue(): void
    {
        $this->dateUpdate = new DateTimeImmutable();
    }
}
