<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use ReflectionAttribute;
use ReflectionClass;

use function key_exists;

/**
 * Entity definition
 */
final class EntityDef
{
    /**
     * Entity
     */
    public const ENTITY_NAMESPACE = __NAMESPACE__;

    /**
     * Criteria for filtering Selectable collections.
     */
    public const CRITERIA_ASC = Criteria::ASC;
    public const CRITERIA_DESC = Criteria::DESC;

    /**
     * Lengths
     */
    /** @var int STR_MAX_LENGTH Максимальная длина строки */
    public const STR_MAX_LENGTH = 255;
    /** @var int STR_MAX_PATH_LENGTH Максимальная длина пути */
    public const STR_MAX_PATH_LENGTH = PHP_MAXPATHLEN;
    /** @var int URL_MAX_LENGTH Максимальная длина URL */
    public const URL_MAX_LENGTH = 2048;
    /** @var int STR_EMAIL_MAX_LENGTH Максимальная длина E-mail */
    public const STR_EMAIL_MAX_LENGTH = 254;
    /** @var int STR_PHONE_MAX_LENGTH Максимальная длина телефона */
    public const STR_PHONE_MAX_LENGTH = 16;

    /**
     * Entity column name
     */
    public const COL_ID = 'id';
    public const COL_NAME = 'name';
    public const COL_ALIAS = 'alias';
    public const COL_UUID = 'uuid';
    public const COL_USER_CREATE = 'userCreate';
    public const COL_DATE_CREATE = 'dateCreate';
    public const COL_USER_UPDATE = 'userUpdate';
    public const COL_DATE_UPDATE = 'dateUpdate';
    public const COL_VERSION = 'version';
    public const COL_DELETED = 'deleted';

    /**
     * Check object or class is entity
     *
     * @param null|object|class-string $objectOrClass Object or class name
     */
    public static function isEntity(object|string|null $objectOrClass): bool
    {
        if (null === $objectOrClass) {
            return false;
        }

        $reflectionClass = new ReflectionClass($objectOrClass);

        $reflectionAttributes = $reflectionClass->getAttributes(ORM\Entity::class, 0);

        if (empty($reflectionAttributes)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get entity attribute arguments for object or class
     *
     * @param null|object|class-string $objectOrClass  Object or class name
     * @param class-string             $attributeClass Attribute class
     */
    public static function getEntityAttributeArguments(
        null|object|string $objectOrClass,
        string $attributeClass,
        null|string $argumentName = null,
    ): null|array|string|int {
        if (null === $objectOrClass) {
            return null;
        }

        if (!self::isEntity($objectOrClass)) {
            return null;
        }

        $reflectionClass = new ReflectionClass($objectOrClass);

        $reflectionAttributes = $reflectionClass->getAttributes($attributeClass, 0);

        $callback = fn (ReflectionAttribute $reflectionAttribute): array => $reflectionAttribute->getArguments();
        /** @var array $attributeClassArguments */
        $attributeClassArguments = array_map($callback, $reflectionAttributes);

        if (count($attributeClassArguments) > 0) {
            /** @var array $attributeClassArguments */
            $attributeClassArguments = $attributeClassArguments[0];
            if (isset($argumentName)) {
                if (key_exists($argumentName, $attributeClassArguments)) {
                    /** @var string|int|null */
                    return $attributeClassArguments[$argumentName];
                } else {
                    return null;
                }
            } else {
                return $attributeClassArguments;
            }
        }

        return null;
    }

    /**
     * Get entity table schema for object or class
     *
     * @param null|object|class-string $objectOrClass Object or class name
     */
    public static function getEntityTableSchema(null|object|string $objectOrClass): null|string
    {
        /** @var null|string */
        return self::getEntityAttributeArguments(
            objectOrClass: $objectOrClass,
            attributeClass: ORM\Table::class,
            argumentName: 'schema',
        );
    }

    /**
     * Get entity table name for object or class
     *
     * @param null|object|class-string $objectOrClass Object or class name
     */
    public static function getEntityTableName(null|object|string $objectOrClass): null|string
    {
        /** @var null|string */
        return self::getEntityAttributeArguments(
            objectOrClass: $objectOrClass,
            attributeClass: ORM\Table::class,
            argumentName: 'name',
        );
    }

    /**
     * Get entity table name with schema for object or class
     *
     * @param null|object|class-string $objectOrClass Object or class name
     */
    public static function getEntityTableNameWidthSchema(null|object|string $objectOrClass): null|string
    {
        $tableName = self::getEntityTableName($objectOrClass);
        $schemaName = self::getEntityTableSchema($objectOrClass);
        if (null !== $schemaName && null !== $tableName) {
            $tableName = $schemaName . '.' . $tableName;
        }

        /** @var null|string */
        return $tableName;
    }

    /**
     * Get entity comment for object or class
     *
     * @param null|object|class-string $objectOrClass Object or class name
     */
    public static function getEntityComment(null|object|string $objectOrClass): null|string
    {
        if (empty($objectOrClass)) {
            return null;
        }

        /** @var array $options */
        $options = self::getEntityAttributeArguments(
            objectOrClass: $objectOrClass,
            attributeClass: ORM\Table::class,
            argumentName: 'options',
        );

        if (key_exists('comment', $options)) {
            return (string) $options['comment'];
        }

        return null;
    }
}
