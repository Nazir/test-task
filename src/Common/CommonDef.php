<?php

declare(strict_types=1);

namespace App\Common;

use DateTimeInterface;

/**
 * Common definition
 */
final class CommonDef
{
    /**
     * Date or (and) time formats
     */
    public const DATE_FORMAT = 'd.m.Y';
    public const TIME_FORMAT = 'H:i:s';
    public const DATE_TIME_FORMAT = 'd.m.Y H:i:s';
    public const DATE_TIME_FILENAME_FORMAT = 'Y-m-d-H-i-s-u';
    public const API_DATE_TIME_FORMAT = DateTimeInterface::W3C;
    public const API_DATE_FORMAT = 'Y-m-d';

    /**
     * Limits for pagination
     */
    /** @var int DATA_LIST_LIMIT Default limit */
    public const DATA_LIST_LIMIT = 10;
    /** @var int DATA_LIST_LIMIT_MAX Maximum limit */
    public const DATA_LIST_LIMIT_MAX = 50;

    /**
     * Other
     */
    public const FILENAME = 'filename';
    public const DIRECTORY = 'directory';
    public const API_EXISTS = 'exists';

    /**
     * @var int FILE_MAX_SIZE Maximum size for file
     */
    public const FILE_MAX_SIZE = 50 * 1024 * 1024;
}
