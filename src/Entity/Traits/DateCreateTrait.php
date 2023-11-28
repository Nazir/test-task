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

/**
 * Need for class: https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-callbacks:
 * #[ORM\HasLifecycleCallbacks]
 */
trait DateCreateTrait
{
    /**
     * Date of record creation in the database
     */
    #[ORM\Column(
        name: '_date_create',
        type: Types::DATETIME_MUTABLE,
        unique: false,
        nullable: false,
        options: [
            'comment' => 'Date of record creation in the database',
            'default' => 'CURRENT_TIMESTAMP',
        ],
    )]
    #[Assert\DateTime]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    #[Context(
        normalizationContext: SerializerDef::API_DATE_TIME_NORMALIZATION_CONTEXT,
        denormalizationContext: SerializerDef::API_DATE_TIME_DENORMALIZATION_CONTEXT,
    )]
    protected DateTimeInterface|null $dateCreate = null;

    public function getDateCreate(): DateTimeInterface|null
    {
        return $this->dateCreate;
    }

    #[ORM\PrePersist]
    public function presetDateCreate(): void
    {
        $this->dateCreate = new DateTimeImmutable();
    }

    public function setDateCreate(DateTimeInterface|null $dateCreate = null): self
    {
        $this->dateCreate = $dateCreate ?? new DateTimeImmutable();

        return $this;
    }
}
