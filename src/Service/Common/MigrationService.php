<?php

namespace App\Service\Common;

use App\Entity\DbDef;
use App\Entity\EntityDef;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;

/**
 * Migration service
 *
 * @author Nazir Khusnutdinov
 */
final class MigrationService
{
    public function __construct(
        private Schema &$schema,
        private AbstractPlatform $platform,
    ) {
    }

    /**
     * Create table
     */
    public function createTable(
        string $name,
        string $schema = DbDef::DB_SCHEMA,
        null|string $comment = null,
        bool $withId = true,
    ): Table {
        $name = $schema === DbDef::DB_SCHEMA ? $name : "$schema.$name";
        $table = $this->schema->createTable($name);
        $table->setComment($comment);

        if (true === $withId) {
            $this->addIdColumn(table: $table);
        }

        return $table;
    }

    /**
     * Drop table
     */
    public function dropTable(
        string $name,
        string $schema = DbDef::DB_SCHEMA,
    ): Schema {
        $name = $schema === DbDef::DB_SCHEMA ? $name : "$schema.$name";
        return $this->schema->dropTable($name);
    }

    /**
     * Add column
     */
    public function addColumn(
        Table &$table,
        string $name,
        string $type,
        null|int $length = null,
        null|bool $notNull = null,
        bool $uniqueConstraint = false,
        mixed $default = null,
        null|string $comment = null,
    ): void {
        $column = $table->addColumn($name, $type);

        $column
            ->setLength($length)
            ->setDefault($default);

        if (null !== $notNull) {
            $column
                ->setNotnull($notNull);
        }

        if (true === $uniqueConstraint) {
            $this->addUniqueConstraint(table: $table, columnNames: [$name]);
        }

        $column->setComment($comment);
    }

    /**
     * Add ID column
     */
    public function addIdColumn(
        Table &$table,
        bool $setPrimaryKey = true,
        bool $autoincrement = true,
    ): void {
        $table
            ->addColumn(DbDef::TBL_COL_ID_NAME, DbDef::TBL_COL_ID_TYPE)
            ->setAutoincrement($autoincrement)
            ->setComment('ID (Identifier)');

        if (true === $setPrimaryKey) {
            $this->setPrimaryKey($table);
        }
    }

    /**
     * Sets the Primary Key.
     *
     * @param string[]     $columnNames
     * @param string|false $indexName
     */
    public function setPrimaryKey(
        Table &$table,
        array $columnNames = [DbDef::TBL_COL_ID_NAME],
        string|false $indexName = false,
    ): void {
        $indexName = $indexName ? $indexName : DbDef::PREFIX_PK . $table->getShortestName($table->getNamespaceName());
        $table->setPrimaryKey($columnNames, $indexName);
    }

    /**
     * Add index
     *
     * @param string[] $columnNames
     * @param string[] $flags
     * @param mixed[]  $options
     */
    public function addIndex(
        Table &$table,
        array $columnNames,
        null|string $indexName = null,
        array $flags = [],
        array $options = []
    ): void {
        if (!empty($columnNames)) {
            $indexNameResult = DbDef::PREFIX_IDX . $table->getShortestName($table->getNamespaceName());
            if (isset($indexName)) {
                if (!empty($indexName)) {
                    $indexNameResult .= '_' . $indexName;
                }
            } else {
                $indexNameResult .= '_' . $columnNames[0];
            }

            $table->addIndex($columnNames, $indexNameResult, $flags, $options);
        }
    }

    /**
     * Add unique constraint
     *
     * @param string[] $columnNames
     * @param string[] $flags
     * @param mixed[]  $options
     */
    public function addUniqueConstraint(
        Table &$table,
        array $columnNames,
        null|string $indexName = null,
        array $flags = [],
        array $options = [],
    ): void {
        if (!empty($columnNames)) {
            $indexNameResult = DbDef::PREFIX_UNIQ . $table->getShortestName($table->getNamespaceName());
            if (isset($indexName)) {
                if (!empty($indexName)) {
                    $indexNameResult .= '_' . $indexName;
                }
            } else {
                $indexNameResult .= '_' . $columnNames[0];
            }

            $table->addUniqueConstraint($columnNames, $indexNameResult, $flags, $options);
        }
    }

    /**
     * Adds a foreign key constraint.
     *
     * @param Table|string $foreignTable       Table schema instance or table name
     * @param string[]     $localColumnNames
     * @param string[]     $foreignColumnNames
     * @param mixed[]      $options
     * @param null|string  $name
     */
    public function addForeignKeyConstraint(
        Table &$table,
        Table|string $foreignTable,
        null|string $foreignTableSchema,
        array $localColumnNames,
        array $foreignColumnNames,
        array $options = [],
        null|string $name = null,
        null|string $indexName = null,
    ): void {
        if (!empty($localColumnNames) && !empty($foreignColumnNames)) {
            $nameResult = DbDef::PREFIX_FK . $table->getShortestName($table->getNamespaceName());
            if (isset($name)) {
                if (!empty($name)) {
                    $nameResult .= '_' . $name;
                }
            } else {
                $nameResult .= '_' . $localColumnNames[0];
            }

            $this->addIndex(
                table: $table,
                columnNames: $localColumnNames,
                indexName: $indexName,
            );

            if (is_string($foreignTable)) {
                $foreignTable = ($foreignTableSchema ?? DbDef::DB_SCHEMA) . '.' . $foreignTable;
            }

            $table->addForeignKeyConstraint(
                $foreignTable,
                $localColumnNames,
                $foreignColumnNames,
                $options,
                $nameResult,
            );
        }
    }

    /**
     * Add date create column
     */
    public function addDateCreateColumn(Table &$table): void
    {
        $table
            ->addColumn('_date_create', Types::DATETIME_MUTABLE)
            ->setNotnull(true)
            ->setDefault($this->platform->getCurrentTimestampSQL())
            ->setComment('Date of record creation in the database');
    }

    /**
     * Add date update column
     */
    public function addDateUpdateColumn(Table &$table): void
    {
        $table
            ->addColumn('_date_update', Types::DATETIME_MUTABLE)
            ->setNotnull(false)
            ->setComment('Date of record update in the database');
    }

    /**
     * Add timestamps columns
     */
    public function addTimestampsColumns(Table &$table): void
    {
        $this->addDateCreateColumn($table);
        $this->addDateUpdateColumn($table);
    }

    /**
     * Add user create column
     */
    public function addUserCreateColumn(Table &$table): void
    {
        $this->addColumn(
            table: $table,
            name: '_user_create',
            type: Types::INTEGER,
            comment: 'User ID when creating a record',
            notNull: true,
        );

        $this->addForeignKeyConstraint(
            table: $table,
            foreignTable: 'user',
            foreignTableSchema: DbDef::DB_SCHEMA,
            localColumnNames: ['_user_create'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
    }

    /**
     * Add user update column
     */
    public function addUserUpdateColumn(Table &$table): void
    {
        $this->addColumn(
            table: $table,
            name: '_user_update',
            type: Types::INTEGER,
            comment: 'User ID when updating a record',
            notNull: false,
        );

        $this->addForeignKeyConstraint(
            table: $table,
            foreignTable: 'user',
            foreignTableSchema: DbDef::DB_SCHEMA,
            localColumnNames: ['_user_update'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
    }

    /**
     * Add users columns
     */
    public function addUsersColumns(Table &$table): void
    {
        $this->addUserCreateColumn($table);
        $this->addUserUpdateColumn($table);
    }

    /**
     * Add deleted column
     */
    public function addDeletedColumn(Table &$table): void
    {
        $table
            ->addColumn('_deleted', Types::DATE_IMMUTABLE)
            ->setNotnull(false)
            ->setComment('The record is deleted?');
    }

    /**
     * Add auxiliary columns
     */
    public function addAuxiliaryColumns(Table &$table): void
    {
        $table
            ->addColumn('_version', Types::INTEGER)
            ->setDefault(1)
            ->setComment('Doctrine. Marker attribute that defines a specified column as version attribute used in an optimistic locking scenario.'); // phpcs:ignore Generic.Files.LineLength.TooLong
    }

    /**
     * Add base columns
     */
    public function addBaseColumns(Table &$table, bool $withId = false): void
    {
        if (true === $withId) {
            $this->addIdColumn(table: $table, setPrimaryKey: true);
        }
        $this->addUsersColumns(table: $table);
        $this->addTimestampsColumns(table: $table);
        $this->addAuxiliaryColumns(table: $table);
        $this->addDeletedColumn(table: $table);
    }

    /**
     * Add base columns for references
     */
    public function addBaseReferenceColumns(Table &$table, bool $withId = false): void
    {
        if (true === $withId) {
            $this->addIdColumn(table: $table, setPrimaryKey: true);
        }
        $this->addNameColumn(table: $table, addUniqueConstraint: true);
        $this->addDeletedColumn(table: $table);
    }


    /**
     * Add base columns for system references
     */
    public function addBaseSystemReferenceColumns(Table &$table, bool $withId = false): void
    {
        if (true === $withId) {
            $this->addIdColumn(table: $table, setPrimaryKey: true);
        }
        $this->addNameColumn(table: $table, addUniqueConstraint: false);
        $this->addAliasColumn(table: $table, addUniqueConstraint: true);
        $this->addDeletedColumn(table: $table);
    }

    /**
     * Add name column
     */
    public function addNameColumn(
        Table &$table,
        bool $addUniqueConstraint = true,
        bool $addIndex = false,
    ): void {
        $this->addColumn(
            table: $table,
            name: 'name',
            type: Types::TEXT,
            comment: 'Name',
            notNull: true,
        );

        if (true === $addUniqueConstraint) {
            $this->addUniqueConstraint(
                table: $table,
                columnNames: ['name'],
                indexName: '',
            );
        }

        if (true === $addIndex) {
            $this->addIndex(
                table: $table,
                columnNames: ['name'],
            );
        }
    }

    /**
     * Add alias column
     */
    public function addAliasColumn(
        Table &$table,
        bool $addUniqueConstraint = true,
        bool $addIndex = false,
    ): void {
        $this->addColumn(
            table: $table,
            name: DbDef::TBL_COL_ALIAS_NAME,
            type: Types::STRING,
            length: EntityDef::STR_MAX_LENGTH,
            comment: 'Alias',
            notNull: true,
        );

        if (true === $addUniqueConstraint) {
            $this->addUniqueConstraint(
                table: $table,
                columnNames: [DbDef::TBL_COL_ALIAS_NAME],
                indexName: DbDef::TBL_COL_ALIAS_NAME,
            );
        }

        if (true === $addIndex) {
            $this->addIndex(
                table: $table,
                columnNames: [DbDef::TBL_COL_ALIAS_NAME],
            );
        }
    }

    /**
     * Add user ID column
     */
    public function addUserIdColumn(
        Table &$table,
    ): void {
        $this->addColumn(
            table: $table,
            name: 'user_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'User ID',
            notNull: true,
        );

        $this->addForeignKeyConstraint(
            table: $table,
            foreignTable: 'user',
            foreignTableSchema: DbDef::DB_SCHEMA,
            localColumnNames: ['user_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
    }
}
