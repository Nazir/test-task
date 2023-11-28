<?php

declare(strict_types=1);

namespace App\Service\Common;

use App\Common\CommonDef;
use App\Exception\ValidationException;
use App\Serializer\Normalizer as AppNormalizer;
use App\Serializer\SerializerDef;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer as Normalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

use function array_merge;
use function is_string;
use function join;

/**
 * Serializer service
 *
 * @author Nazir Khusnutdinov
 */
final class SerializerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Get context for Serializer
     *
     * @param array             $context           Context
     * @param null|array|string $groups            Groups
     * @param null|array        $ignoredAttributes Ignored attributes
     * @param null|bool         $enableMaxDepth    Enable max depth
     *
     * @return array Serializer context
     */
    public static function serializerContext(
        array $context = [],
        null|array|string $groups = SerializerDef::DEFAULT_GROUPS,
        null|array $ignoredAttributes = SerializerDef::DEFAULT_IGNORED_ATTRIBUTES,
        null|bool $enableMaxDepth = true,
    ): array {
        if (isset($groups)) {
            $groups = is_string($groups) ? [$groups] : $groups;
            $context = array_merge($context, [
                Normalizer\AbstractNormalizer::GROUPS => $groups,
            ]);
        }

        if (isset($ignoredAttributes)) {
            $context = array_merge($context, [
                Normalizer\AbstractNormalizer::IGNORED_ATTRIBUTES => $ignoredAttributes,
            ]);
        }

        if (isset($enableMaxDepth)) {
            $context = array_merge($context, [
                Normalizer\AbstractObjectNormalizer::ENABLE_MAX_DEPTH => $enableMaxDepth,
            ]);
        }

        return $context;
    }

    /**
     * Get context for Normalize
     *
     * @param array             $context           Context
     * @param null|array|string $groups            Groups
     * @param null|array        $ignoredAttributes Ignored attributes
     * @param null|bool         $enableMaxDepth    Enable max depth
     *
     * @return array Serializer context
     */
    public static function serializerNormalizeContext(
        array $context = [],
        null|array|string $groups = SerializerDef::DEFAULT_GROUPS,
        null|array $ignoredAttributes = SerializerDef::NORMALIZE_DEFAULT_IGNORED_ATTRIBUTES,
        null|bool $enableMaxDepth = true,
    ): array {
        return self::serializerContext(
            context: $context,
            groups: $groups,
            ignoredAttributes: $ignoredAttributes,
            enableMaxDepth: $enableMaxDepth,
        );
    }

    /**
     * Get context for Normalize
     *
     * @param array             $context           Context
     * @param null|array|string $groups            Groups
     * @param null|array        $ignoredAttributes Ignored attributes
     * @param null|bool         $enableMaxDepth    Enable max depth
     *
     * @return array Serializer context
     */
    public static function serializerDeserializeContext(
        array $context = [],
        null|array|string $groups = SerializerDef::CRUD_GROUPS,
        null|array $ignoredAttributes = SerializerDef::OBJECT_TO_POPULATE_DEFAULT_IGNORED_ATTRIBUTES,
        null|bool $enableMaxDepth = true,
    ): array {
        return self::serializerContext(
            context: $context,
            groups: $groups,
            ignoredAttributes: $ignoredAttributes,
            enableMaxDepth: $enableMaxDepth,
        );
    }

    public static function objectNormalizer(): Normalizer\ObjectNormalizer
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader());

        // $objectNormalizer = new ObjectNormalizer($classMetadataFactory);
        // $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        // $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter);
        // $objectNormalizer = new ObjectNormalizer(
        //     $classMetadataFactory,
        //     $metadataAwareNameConverter,
        //     null,
        //     new ReflectionExtractor()
        // );

        return new Normalizer\ObjectNormalizer(
            $classMetadataFactory,
            null,
            null,
            new ReflectionExtractor(),
        );
    }

    public static function serializer(
        null|string $datetimeFormat = CommonDef::API_DATE_TIME_FORMAT,
        null|EntityManagerInterface $entityManager = null,
    ): Serializer {
        $objectNormalizer = self::objectNormalizer();

        $normalizers = [
            new Normalizer\DateIntervalNormalizer(),
            new Normalizer\UidNormalizer(),
            // new Normalizer\GetSetMethodNormalizer(), // Don`t use. Throws an exception: circular reference has been.
            new Normalizer\ArrayDenormalizer(),
            new AppNormalizer\GetterNormalizer(),
        ];

        if (isset($datetimeFormat)) {
            $normalizers = [
                ...$normalizers,
                ...[
                    new Normalizer\DateTimeNormalizer([Normalizer\DateTimeNormalizer::FORMAT_KEY => $datetimeFormat]),
                ]
            ];
        }

        if (isset($entityManager)) {
            $normalizers = [
                ...$normalizers,
                ...[
                    // new AppNormalizer\DoctrineEntityDenormalizer($entityManager),
                    new AppNormalizer\DoctrineEntityDenormalizer($objectNormalizer, $entityManager),
                ]
            ];
        }

        $normalizers = [
            ...$normalizers,
            ...[
                $objectNormalizer,
            ]
        ];

        $encoders = [
            JsonEncoder::FORMAT => new JsonEncoder(),
            new JsonEncode(),
            new JsonDecode(),
        ];

        return new Serializer($normalizers, $encoders);
    }

    public static function normalize(
        mixed $data,
        string $format = null,
        null|array $context = null,
        string $datetimeFormat = CommonDef::API_DATE_TIME_FORMAT,
        null|array|string $groups = SerializerDef::DEFAULT_GROUPS,
    ): null|array|string|int|float|bool|ArrayObject {
        if (null === $context) {
            $context = self::serializerNormalizeContext(groups: $groups);
        } elseif (null !== $groups) {
            $context = array_merge($context, [
                Normalizer\AbstractNormalizer::GROUPS => $groups,
            ]);
        }

        return self::serializer(datetimeFormat: $datetimeFormat)
            ->normalize(data: $data, format: $format, context: $context);
    }

    /**
     * @param class-string $type
     */
    public static function denormalize(
        null|object $obj,
        mixed $data,
        string $type,
        null|string $format = null,
        null|array $context = null,
        null|string $datetimeFormat = CommonDef::API_DATE_TIME_FORMAT,
        null|array|string $groups = [SerializerDef::CREATE_GROUP, SerializerDef::UPDATE_GROUP],
        array $ignoredAttributes = SerializerDef::OBJECT_TO_POPULATE_DEFAULT_IGNORED_ATTRIBUTES,
        array $ignoredAttributesExtra = [],
        null|EntityManagerInterface $entityManager = null,
    ): mixed {
        if ($context === null) {
            $ignoredAttributes = [...$ignoredAttributes, ...$ignoredAttributesExtra];
            /** @var array<string, mixed> $context */
            $context = [
                ...self::serializerDeserializeContext(
                    groups: $groups,
                    ignoredAttributes: $ignoredAttributes
                ),
                Normalizer\DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
            ];
        }

        try {
            $serializer = self::serializer(datetimeFormat: $datetimeFormat, entityManager: $entityManager);
            if (null === $obj) {
                /** @var object|null $obj */
                $obj = $serializer->denormalize(data: $data, type: $type, format: $format, context: $context);
            } else {
                $context = [...$context, Normalizer\AbstractNormalizer::OBJECT_TO_POPULATE => $obj];
                $serializer->denormalize(data: $data, type: $type, format: $format, context: $context);
            }
        } catch (PartialDenormalizationException $e) {
            throw self::catchDenormalization(e: $e, type: $type);
        } catch (Throwable $e) {
            throw $e;
        }

        return $obj;
    }

    public static function decode(
        string $data,
        string $format = JsonEncoder::FORMAT,
        null|array $context = null,
        string $datetimeFormat = CommonDef::API_DATE_TIME_FORMAT,
    ): mixed {
        return self::serializer(datetimeFormat: $datetimeFormat)
            ->decode(data: $data, format: $format, context: $context ?? self::serializerNormalizeContext());
    }

    /**
     * @param class-string $type
     */
    public function deserialize(
        null|object $obj,
        mixed $data,
        string $type,
        string $format = JsonEncoder::FORMAT,
        null|array|string $groups = [SerializerDef::CREATE_GROUP, SerializerDef::UPDATE_GROUP],
        null|array $context = null,
        null|string $datetimeFormat = CommonDef::API_DATE_TIME_FORMAT,
        array $ignoredAttributes = SerializerDef::OBJECT_TO_POPULATE_DEFAULT_IGNORED_ATTRIBUTES,
        array $ignoredAttributesExtra = [],
        EntityManagerInterface|null $entityManager = null,
    ): mixed {
        $ignoredAttributes = [...$ignoredAttributes, ...$ignoredAttributesExtra];
        /** @var array<string, mixed> $context */
        $context = [
            ...($context ?? self::serializerDeserializeContext(groups: $groups, ignoredAttributes: $ignoredAttributes)),
            Normalizer\DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
        ];

        try {
            $serializer = self::serializer(
                datetimeFormat: $datetimeFormat,
                entityManager: $entityManager ?? $this->entityManager,
            );
            if (null === $obj) {
                $obj = $serializer->deserialize($data, $type, $format, $context);
            } else {
                $context = [...$context, Normalizer\AbstractNormalizer::OBJECT_TO_POPULATE => $obj];
                $serializer->deserialize($data, $type, $format, $context);
            }
        } catch (PartialDenormalizationException $e) {
            throw $this->catchDenormalization(e: $e, type: $type);
        } catch (Throwable $e) {
            throw $e;
        }

        return $obj;
    }

    protected static function catchDenormalization(
        PartialDenormalizationException $e,
        string $type
    ): ValidationException {
        $violations = new ConstraintViolationList();
        foreach ($e->getErrors() as $exception) {
            $message = sprintf(
                'The type must be one of "%s" ("%s" given) for class "%s".',
                join(', ', $exception->getExpectedTypes() ?? []),
                $exception->getCurrentType() ?? '',
                $type,
            );
            $parameters = [];
            if ($exception->canUseMessageForUser()) {
                $parameters['hint'] = $exception->getMessage();
            }
            $violations->add(new ConstraintViolation($message, '', $parameters, null, $exception->getPath(), null));
        }
        return ValidationException::fromViolations($violations);
    }
}
