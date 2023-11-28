<?php

namespace App\Serializer\Normalizer;

use App\Entity\EntityDef;
use App\Exception\ValidationException;
// use App\Service\Common\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
// use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use function is_array;
use function is_null;
use function is_numeric;
use function is_string;
use function key_exists;

class DoctrineEntityDenormalizer implements DenormalizerInterface/*, DenormalizerAwareInterface*/
{
    use DenormalizerAwareTrait;

    public function __construct(
        ObjectNormalizer $denormalizer,
        protected EntityManagerInterface $entityManager,
    ) {
        $this->setDenormalizer($denormalizer);
        $this->setEntityManager($entityManager);
    }

    // public function __construct(
    //     protected EntityManagerInterface $entityManager,
    // ) {
    //     $denormalizer = SerializerService::objectNormalizer();

    //     $this->setDenormalizer($denormalizer);
    //     $this->setEntityManager($entityManager);
    // }

    /**
     * @see DenormalizerInterface
     */
    public function denormalize(
        mixed $data,
        string $type,
        string $format = null,
        array $context = [],
        null|object $entity = null,
    ): null|object {
        /** @var class-string $type */
        // if (false === EntityDef::isEntity($type)) {
        //     return null;
        // }

        /** @var class-string $type */
        // if ($type === \Doctrine\ORM\PersistentCollection::class) {
        //     $column = $context['deserialization_path'];
        //     if (!empty($column)) {
        //         // EntityDef::getColumnTargetEntity()
        //     }
        // } else {
        //     $repository = $this->getRepository($type);
        // }
        $repository = $this->getRepository($type);
        $typeComment = EntityDef::getEntityComment($type);
        $typeInfo = isset($typeComment) ? "$typeComment [$type]" : $type;
        if (!$repository instanceof ObjectRepository) {
            throw new ValidationException("No repository found for given type, {$typeInfo}.");
        }

        if (null === $entity) {
            if (is_numeric($data) || is_string($data)) {
                $entity = $repository->find($data);
            } elseif (is_array($data) && key_exists(EntityDef::COL_ID, $data)) {
                /** @var null|int $id */
                $id = $data[EntityDef::COL_ID];
                if (isset($id)) {
                    $entity = $repository->find($data[EntityDef::COL_ID]);
                } else {
                    throw new ValidationException("ID is NULL of type, {$typeInfo}.");
                }
                if (is_null($entity)) {
                    throw new ValidationException("Object not found for given ID [$id] of type, $typeInfo .");
                }

                if (1 === count($data)) {
                    return $entity;
                }
            }
        }
        // Denormalize into the found entity with given data by using the default ObjectNormalizer
        $tmpContext = [...$context, ...[
            AbstractNormalizer::OBJECT_TO_POPULATE => $entity,
        ]];

        /** @var object $entity */
        $entity = $this->denormalizer->denormalize($data, $type, $format, $tmpContext);

        return $entity;
    }

    /**
     * @see DenormalizerInterface
     */
    public function supportsDenormalization(
        mixed $data,
        string $type,
        string $format = null,
        array $context = [],
    ): bool {
        // Check that it s an Entity of our App and a Repository exist for it
        // Also only use the denormalizer if an ID is set to load from the Repository.

        // /** @var class-string $type */
        // $result = (EntityDef::isEntity($type) && !is_null($this->getRepository($type))
        //     && (is_numeric($data) || is_string($data)
        //         || (is_array($data) && isset($data[EntityDef::COL_ID]))
        //     ))
        //     || ($type === \Doctrine\ORM\PersistentCollection::class && is_array($data));

        // return $result;

        /** @var class-string $type */
        return EntityDef::isEntity($type) /*strpos($type, 'App\\Entity\\') === 0*/
            && !is_null($this->getRepository($type))
            && (is_numeric($data) || is_string($data)
                || (is_array($data) && isset($data[EntityDef::COL_ID])));
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param class-string $class
     */
    protected function getRepository(string $class): null|ObjectRepository
    {
        $result = null;
        try {
            $result = $this->entityManager->getRepository($class);
        } catch (Exception $e) {
            // Manager could not be resolved
        }

        return $result;
    }

    /**
     * @see DenormalizerInterface
     */
    public function getSupportedTypes(string|null $format): array
    {
        $isCacheable = __CLASS__ === static::class;

        return ['object' => $isCacheable];
    }
}
