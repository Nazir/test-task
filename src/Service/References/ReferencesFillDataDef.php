<?php

namespace App\Service\References;

use App\Modules\Order\Entity\OrderStatus;

final class ReferencesFillDataDef
{
    /**
     * @param array<array-key, array{class: class-string, data: array<array-key, string>}> INTRASYSTEM_ENTITY
     */
    public const INTRASYSTEM_ENTITY = [
        // Статус заявки
        'order-status' => [
            'class' => OrderStatus::class,
            'data' => ReferencesDef::ORDER_STATUSES,
        ],
    ];
}
