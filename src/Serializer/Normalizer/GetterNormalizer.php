<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use ArrayObject;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function is_object;
use function key_exists;
use function method_exists;

/**
 * Normalizer for getters
 */
class GetterNormalizer implements NormalizerInterface
{
    use NormalizerAwareTrait;

    public const GETTER_METHOD_NAME_KEY = 'getter_method_name';

    /**
     * @see NormalizerInterface
     */
    public function normalize(
        mixed $object,
        string $format = null,
        array $context = []
    ): null|array|string|int|float|bool|ArrayObject {
        if (false === key_exists(self::GETTER_METHOD_NAME_KEY, $context)) {
            return null;
        }

        /** @var string */
        $method = $context[self::GETTER_METHOD_NAME_KEY];
        /** @var null|int */
        return is_object($object) && method_exists($object, $method) ? $object->{$method}() : null;
    }

    /**
     * @see NormalizerInterface
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (false === key_exists(self::GETTER_METHOD_NAME_KEY, $context)) {
            return false;
        }

        /** @var string */
        $method = $context[self::GETTER_METHOD_NAME_KEY];

        return is_object($data) && method_exists($data, $method);
    }

    /**
     * @see NormalizerInterface
     */
    public function getSupportedTypes(string|null $format): array
    {
        $isCacheable = __CLASS__ === static::class;

        return ['object' => $isCacheable];
    }
}
