<?php

declare(strict_types=1);

namespace App\Utils;

use App\Exception as Except;

use function array_filter;
use function array_map;
use function is_array;
use function key_exists;

final class ArrayUtils
{
    public const TYPE_ARRAY = 0;
    public const TYPE_STRING = 1;
    public const TYPE_INTEGER = 2;
    public const TYPE_FLOAT = 3;

    /**
     * Get value from array by array-key
     *
     * @param array-key      $key       Key of array
     * @param null|array     $array     Array
     * @param null|array-key $subKey    Sub key of array
     * @param bool           $nullable  Nullable
     * @param null|int       $type      Type of value
     *
     * @param bool $throw Throw an exception
     *
     * @return mixed Value
     *
     * @throws Except\ValidationException
     */
    public static function getValue(
        string $key,
        null|array $array,
        null|string $subKey = null,
        bool $nullable = false,
        null|int $type = self::TYPE_STRING,
        bool $throw = true,
    ): mixed {
        if (null === $array && !$nullable && true === $throw) {
            throw new Except\ValidationException('Array is NULL');
        }

        if (null === $array) {
            return null;
        }

        /** @var mixed $value */
        $value = null;
        if (key_exists($key, $array)) {
            if (isset($subKey)) {
                /** @var null|array $subArray */
                $subArray = $array[$key];
                if (is_array($subArray) && key_exists($subKey, $subArray)) {
                    /** @var mixed $value */
                    $value = $subArray[$subKey];
                } else {
                    if (!$nullable && true === $throw) {
                        throw new Except\ValidationException([$subKey => 'Array is NULL for']);
                    }
                }
            } else {
                /** @var mixed $value */
                $value = $array[$key];
            }
        }

        if (null === $value && !$nullable && true === $throw) {
            throw new Except\ValidationException([$subKey ?? $key => 'Value is NULL']);
        }

        if (isset($value) && isset($type)) {
            switch ($type) {
                case self::TYPE_ARRAY:
                    if (!is_array($value) && true === $throw) {
                        throw new Except\ValidationException([$subKey ?? $key => 'Value is not array']);
                    }
                    $value = (array) $value;
                    break;
                case self::TYPE_STRING:
                    if (!is_string($value) && true === $throw) {
                        throw new Except\ValidationException([$subKey ?? $key => 'Value is not string']);
                    }
                    $value = (string) $value;
                    break;
                case self::TYPE_INTEGER:
                    if ((string)(int) $value !== (string) $value && true === $throw) {
                        throw new Except\ValidationException([$subKey ?? $key => 'Value is not integer']);
                    }
                    $value = (int) $value;
                    break;
                case self::TYPE_FLOAT:
                    if (is_float($value) && true === $throw) {
                        throw new Except\ValidationException([$subKey ?? $key => 'Value is not float']);
                    }
                    $value = (float) $value;
                    break;
                default:
                    break;
            }
        }

        return $value;
    }

    /**
     * Get IDs values from array by array-key
     *
     * @param array-key      $key       Key of array
     * @param null|array     $array     Array
     * @param null|array-key $subKey    Sub key of array
     * @param bool           $nullable  Nullable
     *
     * @param bool $throw Throw an exception
     *
     * @return null|int[]|string[] IDs values
     *
     * @throws Except\ValidationException
     */
    public static function getIds(
        string $key,
        null|array $array,
        null|string $subKey = null,
        bool $nullable = false,
        bool $throw = true,
    ): null|array {
        if (null === $array && true === $throw) {
            throw new Except\ValidationException([$subKey ?? $key => 'Array is NULL']);
        }

        if (null === $array) {
            return null;
        }

        /** @var int[] $value */
        $value = null;
        if (key_exists($key, $array)) {
            if (isset($subKey)) {
                /** @var array $array */
                $array = $array[$key];
                if (key_exists($subKey, $array)) {
                    /** @var mixed $value */
                    $value = $array[$subKey];
                }
            } else {
                /** @var mixed $value */
                $value = $array[$key];
            }
        }

        if (null === $value && !$nullable && true === $throw) {
            throw new Except\ValidationException([$subKey ?? $key => 'Value is NULL']);
        }

        if (null !== $value) {
            if (is_array($value) && array_filter($value, 'is_numeric')) {
                return array_map(function ($v) {
                    return (int) $v;
                }, $value);
            } elseif (is_array($value) && array_filter($value, 'is_string')) {
                return array_map(function ($v) {
                    return (string) $v;
                }, $value);
            } else {
                if (true === $throw && false === $nullable) {
                    throw new Except\ValidationException([$subKey ?? $key => 'IDs not found']);
                }
                $value = null;
            }
        }

        return $value;
    }
}
