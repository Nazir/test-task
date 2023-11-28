<?php

declare(strict_types=1);

namespace App\Service\References;

final class ReferencesDef
{
    /**
     * @var array<string, class-string> MAP
     */
    public const MAP = ReferencesMapDef::MAP
        + ReferencesMapOrderDef::MAP;

    /**
     * Статус заявки
     */
    public const ORDER_STATUS_WAITING_TO_BE_PROCESSED = 'waiting-to-be-processed';
    public const ORDER_STATUS_WAITING_TO_BE_SENT = 'waiting-to-be-sent';
    public const ORDER_STATUS_DISPATCHED = 'dispatched';
    public const ORDER_STATUS_ISSUED_TO_CUSTOMER = 'issued-to-customer';

    /**
     * @var array<string, string> ORDER_STATUSES Статусы заявки
     */
    public const ORDER_STATUSES = [
        self::ORDER_STATUS_WAITING_TO_BE_PROCESSED => 'Ожидает обработки',
        self::ORDER_STATUS_WAITING_TO_BE_SENT => 'Ожидает отправки',
        self::ORDER_STATUS_DISPATCHED => 'Отправлено',
        self::ORDER_STATUS_ISSUED_TO_CUSTOMER => 'Выдано клиенту',
    ];
}
