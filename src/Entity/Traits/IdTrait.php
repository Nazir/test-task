<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\DbDef;
use App\Serializer\SerializerDef;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait IdTrait
{
    /**
     * ID (Identifier)
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(
        name: 'id',
        type: DbDef::TBL_COL_ID_TYPE,
        options: ['comment' => 'ID (Identifier)'],
    )]
    #[Groups(SerializerDef::ID_GROUP)]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
