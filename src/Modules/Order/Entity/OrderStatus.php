<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Entity\DbDef;
use App\Entity\References\Interfaces as RefI;
use App\Entity\References\Traits as RefTraits;
use App\Modules\Order\Repository\OrderStatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderStatus
 * Статус заявки
 */
#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
#[ORM\Table(
    name: OrderDef::TBL_NAME_ORDER_STATUS,
    schema: OrderDef::DB_SCHEMA,
    options: ['comment' => 'Статус заявки'],
)]
#[ORM\UniqueConstraint(name: DbDef::PREFIX_UNIQ . OrderDef::TBL_NAME_ORDER_STATUS, columns: ['name'])]
#[ORM\UniqueConstraint(
    name: DbDef::PREFIX_UNIQ . OrderDef::TBL_NAME_ORDER_STATUS . '_' . DbDef::TBL_COL_ALIAS_NAME,
    columns: [DbDef::TBL_COL_ALIAS_NAME],
)]
class OrderStatus implements RefI\SystemReferenceInterface
{
    use RefTraits\BaseSystemReferenceTrait;

    public function __construct(string $name, string $alias)
    {
        $this->name = $name;
        $this->alias = $alias;
    }
}
