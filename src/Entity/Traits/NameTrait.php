<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Serializer\SerializerDef;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait NameTrait
{
    #[ORM\Column(
        name: 'name',
        type: Types::TEXT,
        unique: false,
        nullable: false,
        options: ['comment' => 'Name'],
    )]
    #[Assert\NotBlank()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
