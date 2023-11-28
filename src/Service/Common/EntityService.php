<?php

declare(strict_types=1);

namespace App\Service\Common;

use App\Entity\DbDef;
use App\Entity\EntityDef;
use App\Exception as Except;
use App\Service\BaseService;
use App\Utils\StringUtils;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Contracts\Translation\TranslatorInterface;

use function is_object;

/**
 * Entity service
 *
 * @author Nazir Khusnutdinov
 */
final class EntityService extends BaseService
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        public readonly EntityManagerInterface $entityManager,
        private readonly DbService $dbService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * Save object
     *
     * @param object $obj   Entity object
     * @param bool   $flush Flush changes to database
     */
    public function save(object $obj, bool $flush = true): void
    {
        $this->persist($obj);
        if ($flush) {
            $this->flush();
        }
    }

    /**
     * Finds an object by its identifier.
     *
     * This is just a convenient shortcut for getRepository($className)->find($id).
     *
     * @param class-string $className The class name of the object to find.
     * @param mixed  $id              The identity of the object to find.
     * @psalm-param class-string<T> $className
     *
     * @return object|null The found object.
     * @psalm-return T|null
     *
     * @template T of object
     */
    public function find(string $className, mixed $id): object|null
    {
        return $this->entityManager->find($className, $id);
    }

    /**
     * Persist object
     *
     * @param object $obj Entity object
     */
    public function persist(object $obj): void
    {
        if (!$this->entityManager->isOpen()) {
            $this->managerRegistry->resetManager();
        }
        $this->entityManager->persist($obj);
    }

    /**
     * Remove object
     *
     * @param object $obj   Entity object
     * @param bool   $flush Flush changes to database
     */
    public function remove(object $obj, bool $flush = true): void
    {
        $this->entityManager->remove($obj);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * Clears the ObjectManager. All objects that are currently managed
     * by this ObjectManager become detached.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->entityManager->clear();
    }

    /**
     * Detaches an object from the ObjectManager, causing a managed object to
     * become detached. Unflushed changes made to the object if any
     * (including removal of the object), will not be synchronized to the database.
     * Objects which previously referenced the detached object will continue to
     * reference it.
     *
     * @param object $obj The object to detach.
     *
     * @return void
     */
    public function detach(object $obj): void
    {
        $this->entityManager->detach($obj);
    }

    /**
     * Refresh object
     *
     * @param object $obj Entity object
     */
    public function refresh(object $obj): void
    {
        $this->entityManager->refresh($obj);
    }

    /**
     * Persist object
     */
    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @psalm-param class-string<T> $className
     *
     * @psalm-return \Doctrine\ORM\EntityRepository<T>
     *
     * @template T of object
     */
    public function getRepository(string $className)
    {
        return $this->entityManager->getRepository($className);
    }

    /**
     * Gets the database connection object used by the EntityManager.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->entityManager->getConnection();
    }

    /**
     * Starts a transaction on the underlying database connection.
     *
     * @return void
     */
    public function beginTransaction(): void
    {
        $this->entityManager->beginTransaction();
    }

    /**
     * Commits a transaction on the underlying database connection.
     *
     * @return void
     */
    public function commit(): void
    {
        $this->entityManager->commit();
    }

    /**
     * Performs a rollback on the underlying database connection.
     *
     * @return void
     */
    public function rollback(): void
    {
        $this->entityManager->rollback();
    }

    /**
     * Create object not fount exception
     *
     * @param object|class-string|string $objOrClass   Entity object or class
     * @param string|int                 $id           ID
     * @param null|string                $propertyName Property name
     */
    public function createObjectNotFountException(
        object|string $objOrClass,
        string|int $id,
        null|string $propertyName = null,
        string $fieldName = 'ID',
        int $code = 0,
    ): Except\ValidationException {
        /** @var class-string $className */
        $className = is_object($objOrClass) ? $objOrClass::class : $objOrClass;

        $entity = EntityDef::getEntityComment($className) ?? $className;

        if (empty($entity)) {
            $entity = 'UNKNOWN';
        }

        $message = $this->translator->trans(
            'object.id.not.found',
            [
                '%entity%' => $entity,
                '%id%' => (string) $id,
                '%field%' => $fieldName,
            ]
        );

        if (null !== $propertyName) {
            $message = [$propertyName => $message];
        }

        return new Except\ValidationException(message: $message, code: $code);
    }

    /**
     * Get table name
     *
     * @param class-string $entityClass Class of Entity
     *
     * @return string
     */
    public static function getTableName(string $entityClass, bool $withSchema = true): string
    {
        $tableName = EntityDef::getEntityTableName($entityClass) ?? StringUtils::stringToSnakeCase($entityClass);
        $schema = EntityDef::getEntityTableSchema($entityClass);
        if ($withSchema && isset($schema)) {
            $tableName = $schema . '.' . $tableName;
        }

        return $tableName;
    }

    /**
     * Get property of class
     *
     * @param class-string $entityClass Class of Entity
     * @param string       $name        The property name
     *
     * @return ReflectionProperty|null
     */
    public static function getProperty(string $entityClass, string $name): ReflectionProperty|null
    {
        $class = new ReflectionClass($entityClass);

        if ($class->hasProperty($name)) {
            return $class->getProperty($name);
        }

        return null;
    }

    /**
     * Get table schema
     *
     * @param class-string $entityClass Class of Entity
     *
     * @return string
     */
    public static function getTableSchema(string $entityClass): string
    {
        return EntityDef::getEntityTableSchema($entityClass) ?? DbDef::DB_SCHEMA;
    }

    /**
     * Truncate table
     *
     * @param class-string $entityClass Entity class
     * @param bool         $cascade     Cascade
     */
    public function truncateTable(string $entityClass, bool $cascade = false): void
    {
        $connection = $this->entityManager->getConnection();

        $table = $connection->quoteIdentifier(self::getTableName(entityClass: $entityClass));
        $this->dbService->truncateTable(table: $table, cascade: $cascade);
    }
}
