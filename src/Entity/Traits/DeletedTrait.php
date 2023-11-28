<?php

namespace App\Entity\Traits;

use App\Serializer\SerializerDef;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait DeletedTrait
{
    /**
     * The record is deleted?
     */
    #[ORM\Column(
        name: '_deleted',
        type: Types::DATE_IMMUTABLE,
        nullable: true,
        options: [
            'comment' => 'The record is deleted?'
        ],
    )]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    protected null|DateTimeInterface $deleted = null;

    public function getDeleted(): null|DateTimeInterface
    {
        return $this->deleted;
    }

    public function setDeleted(null|DateTimeInterface $deleted = null, bool $restore = false): self
    {
        if (true === $restore) {
            $this->deleted = null;
        } else {
            $this->deleted = $deleted ?? new DateTimeImmutable();
        }

        return $this;
    }
}
