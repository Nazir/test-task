<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;

/**
 * Database definition
 */
final class DbDef
{
    /**
     * @param array<string, int> DB_TYPE_TO_PARAM_TYPE
     */
    public const DB_TYPE_TO_PARAM_TYPE =
    [
        Types::INTEGER => ParameterType::INTEGER,
        Types::STRING => ParameterType::STRING,
        Types::TEXT => ParameterType::STRING,
        Types::BOOLEAN => ParameterType::BOOLEAN,
        Types::DATE_MUTABLE => ParameterType::STRING,
        Types::JSON => ParameterType::STRING,
        Types::DECIMAL => ParameterType::STRING,
    ];

    /**
     * Database
     */
    public const DB_SCHEMA = 'public';
    public const PREFIX_PK = 'pkey_';
    public const PREFIX_FK = 'fk_';
    public const PREFIX_UNIQ = 'uniq_';
    public const PREFIX_IDX = 'idx_';

    /**
     * Database column type
     */
    public const TBL_COL_ID_NAME = 'id';
    public const TBL_COL_ID_TYPE = Types::INTEGER;
    public const TBL_COL_ALIAS_NAME = 'alias';
    public const TBL_COL_ALIAS_TYPE = Types::TEXT;
    public const TBL_COL_UUID_TYPE = Types::GUID;

    /**
     * Precisions
     */
    public const TBL_COL_DECIMAL_TYPE_DEFAULT_PRECISION = 16;
    public const TBL_COL_DECIMAL_TYPE_DEFAULT_SCALE = 2;
}
