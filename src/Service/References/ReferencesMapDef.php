<?php

declare(strict_types=1);

namespace App\Service\References;

use App\Entity\References as Ref;

final class ReferencesMapDef
{
    public const REF_NAME_CITY = 'city';
    public const REF_NAME_STORAGE = 'storage';

    /**
     * @var array<string, class-string> MAP
     */
    public const MAP = [
        self::REF_NAME_CITY => Ref\City::class,
        self::REF_NAME_STORAGE => Ref\Storage::class,
    ];
}
