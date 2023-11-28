<?php

declare(strict_types=1);

namespace App\Service\References;

use App\Modules\Order\Entity as Ent;

final class ReferencesMapOrderDef
{
    public const REF_NAME_ORDER_STATUS = 'order-status';

    /**
     * @var array<string, class-string> MAP
     */
    public const MAP = [
        self::REF_NAME_ORDER_STATUS => Ent\OrderStatus::class,
    ];
}
