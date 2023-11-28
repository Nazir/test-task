<?php

declare(strict_types=1);

namespace App\Service\Common;

use App\Exception as Except;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use Exception;
use Throwable;

use function is_a;

/**
 * Database service
 *
 * @author Nazir Khusnutdinov
 */
final class DbService
{
    public function __construct(
        public readonly Connection $connection,
    ) {
    }

    /**
     * Get connection
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Executes an SQL statement with the given parameters and returns the number of affected rows.
     *
     * Could be used for:
     *  - DML statements: INSERT, UPDATE, DELETE, etc.
     *  - DDL statements: CREATE, DROP, ALTER, etc.
     *  - DCL statements: GRANT, REVOKE, etc.
     *  - Session control statements: ALTER SESSION, SET, DECLARE, etc.
     *  - Other statements that don't yield a row set.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string                                                               $sql    SQL statement
     * @param list<mixed>|array<string, mixed>                                     $params Statement parameters
     * @param array<int, int|string|Type|null>|array<string, int|string|Type|null> $types  Parameter types
     *
     * @return int|string The number of affected rows.
     *
     * @throws Exception
     */
    public function executeStatement($sql, array $params = [], array $types = [], bool $throw = true): int|string
    {
        try {
            $result = $this->connection->executeStatement($sql, $params, $types);
        } catch (Exception $e) {
            if (true === $throw) {
                throw $e;
            }

            return 0;
            // throw new Except\ValidationException(
            //     message: '',
            // );
        }

        return $result;
    }

    /**
     * Get last insert ID
     *
     * @param bool $throw Throw an exception
     *
     * @throws Except\UnprocessableEntityException
     *
     * @return null|string|int
     */
    public function getLastInsertId(bool $throw = true): null|string|int
    {
        $result = $this->connection->lastInsertId();

        if (false === $result) {
            if (true === $throw) {
                throw new Except\UnprocessableEntityException(
                    message: 'Получить последний идентификатор (ID) вставки не удалось',
                    code: 0, // TODO:
                );
            }

            return null;
        }

        return $result;
    }

    /**
     * Truncate table
     *
     * @param string $table   Table name
     * @param bool   $cascade Cascade
     *
     * @return void
     */
    public function truncateTable(string $table, bool $cascade = false): void
    {
        if (is_a($this->connection->getDatabasePlatform(), PostgreSQLPlatform::class)) {
            $sql = <<<SQL
            TRUNCATE TABLE $table RESTART IDENTITY
            SQL;
            if ($cascade) {
                $sql .= ' CASCADE';
            }
            $sql .= ';';
        } else {
            $sql = $this->connection->getDatabasePlatform()->getTruncateTableSQL(tableName: $table, cascade: $cascade);
        }
        $this->connection->executeQuery($sql);
    }

    /**
     * Get current timestamp SQL
     *
     * @return string
     */
    public function currentTimestampSQL(): string
    {
        $sqlCurrentTimestamp = <<<SQL
        CURRENT_TIMESTAMP()
        SQL;

        return $sqlCurrentTimestamp;
    }

    /**
     * Get current timestamp
     *
     * @return string
     */
    public function currentTimestamp(): string
    {
        $currentTimestamp = new DateTimeImmutable();

        return $this->quote($currentTimestamp->format(
            $this->connection->getDatabasePlatform()->getDateTimeFormatString(),
        ));
    }

    /**
     * Quote value
     *
     * @param mixed $value Value for quote
     *
     * @return string
     */
    public function quote(mixed $value): string
    {
        if (null === $value) {
            return 'NULL';
        }

        return (string) $this->connection->quote($value);
    }

    /**
     * Use transaction
     *
     * @param callable      $function Function to wrap
     * @param null|callable $onCatch  Function to call on catch
     *
     * @param bool $throw Throw an exception
     *
     * @return void
     */
    public function useTransaction(
        callable $function,
        null|callable $onCatch = null,
        null|Connection $connection = null,
        bool $throw = true,
    ): void {
        $connection = $connection ?? $this->connection;

        $connection->setAutoCommit(false);
        $connection->beginTransaction();
        try {
            $function();
            $connection->commit();
        } catch (Throwable $th) {
            $connection->rollBack();
            if (null !== $onCatch) {
                $onCatch($th);
            }
            throw $th;
        } finally {
            $connection->setAutoCommit(true);
        }
    }
}
