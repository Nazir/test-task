<?php

namespace App\Entity\Traits;

use App\Entity\DbDef;
use App\Serializer\SerializerDef;
use App\Utils\StringUtils;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait AliasTrait
{
    #[ORM\Column(
        name: DbDef::TBL_COL_ALIAS_NAME,
        type: DbDef::TBL_COL_ALIAS_TYPE,
        unique: false,
        nullable: false,
        options: ['comment' => 'Alias'],
    )]
    #[Assert\NotNull()]
    #[Assert\Unique()]
    #[Groups(SerializerDef::DEFAULT_GROUPS)]
    private string $alias;

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public static function prepareAlias(string $alias): string
    {
        return StringUtils::slug($alias);
    }
}
