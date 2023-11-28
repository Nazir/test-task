<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

trait AuxiliaryTrait
{
    /**
     * Doctrine. Marker attribute that defines a specified column as version attribute
     * used in an optimistic locking scenario.
     */
    #[ORM\Column(
        name: '_version',
        type: Types::INTEGER,
        unique: false,
        nullable: true,
        options: [
            'comment' => 'Doctrine. Marker attribute that defines a specified column as version attribute used in an optimistic locking scenario.', // phpcs:ignore Generic.Files.LineLength.TooLong
            'default' => 1,
        ],
    )]
    #[ORM\Version]
    #[Ignore]
    protected null|int $version = null;

    public function getVersion(): null|int
    {
        return $this->version;
    }

    public function setVersion(null|int $version = 0): self
    {
        $this->version = $version;

        return $this;
    }
}
