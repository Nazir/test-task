<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Common\CommonDef;
use App\Entity\EntityDef;
// use App\Serializer\Normalizer as AppNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Serializer definition
 */
final class SerializerDef
{
    /**
     * Groups
     */
    public const ALL_GROUP = 'all';
    public const DEFAULT_GROUP = 'default';
    public const DATA_GROUP = 'data';
    public const PUBLIC_GROUP = 'public';
    public const PREVIEW_GROUP = 'preview';
    public const ID_GROUP = 'id';
    /** CRUD */
    public const CREATE_GROUP = 'create';
    public const READ_GROUP = 'read';
    public const UPDATE_GROUP = 'update';
    public const DELETE_GROUP = 'delete';
    public const CRUD_GROUPS = [
        self::CREATE_GROUP,
        self::READ_GROUP,
        self::UPDATE_GROUP,
        self::DELETE_GROUP,
    ];
    public const UPDATE_GROUPS = [
        self::READ_GROUP,
        self::UPDATE_GROUP,
        self::ID_GROUP,
    ];

    public const PREVIEW_GROUPS = [
        self::PUBLIC_GROUP,
        self::PREVIEW_GROUP,
        self::ID_GROUP,
    ];
    public const DEFAULT_GROUPS = [
        ...self::CRUD_GROUPS,
        ...self::PREVIEW_GROUPS,
    ];

    /** @var array DEFAULT_IGNORED_ATTRIBUTES for Serializer Component */
    public const DEFAULT_IGNORED_ATTRIBUTES = [
        EntityDef::COL_VERSION,
    ];

    /** @var array NORMALIZE_DEFAULT_IGNORED_ATTRIBUTES for Serializer Component */
    public const NORMALIZE_DEFAULT_IGNORED_ATTRIBUTES = [...self::DEFAULT_IGNORED_ATTRIBUTES];

    /** @var array OBJECT_TO_POPULATE_DEFAULT_IGNORED_ATTRIBUTES for Serializer Component */
    public const OBJECT_TO_POPULATE_DEFAULT_IGNORED_ATTRIBUTES = [
        ...self::DEFAULT_IGNORED_ATTRIBUTES,
        // EntityDef::COL_ID,
        EntityDef::COL_DATE_UPDATE,
        EntityDef::COL_DATE_CREATE,
    ];

    /**
     * Serializer context
     */
    public const API_DATE_TIME_NORMALIZATION_CONTEXT = [
        DateTimeNormalizer::FORMAT_KEY => CommonDef::API_DATE_TIME_FORMAT,
    ];
    public const API_DATE_TIME_DENORMALIZATION_CONTEXT = [
        DateTimeNormalizer::FORMAT_KEY => CommonDef::API_DATE_TIME_FORMAT,
    ];
    public const API_DATE_NORMALIZATION_CONTEXT = [
        DateTimeNormalizer::FORMAT_KEY => CommonDef::API_DATE_FORMAT,
    ];
    public const API_DATE_DENORMALIZATION_CONTEXT = [
        DateTimeNormalizer::FORMAT_KEY => CommonDef::API_DATE_FORMAT,
    ];
    // public const GETTER_ID_CONTEXT = [
    //     AppNormalizer\GetterNormalizer::GETTER_METHOD_NAME_KEY => 'getId',
    // ];
}
