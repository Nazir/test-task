<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DbDef;
use App\Entity\References\ReferencesDef;
use App\Service\Common\MigrationService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20231111072849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tables "public.city" & "public.storage"';
    }

    public function up(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.city"
         */
        $city = $migrationService->createTable(
            name: ReferencesDef::TBL_NAME_CITY,
            schema: ReferencesDef::DB_SCHEMA,
            comment: 'Город',
        );
        $migrationService->addNameColumn($city);
        $migrationService->addDeletedColumn($city);

        /**
         * Table "public.storage"
         */
        $storage = $migrationService->createTable(
            name: ReferencesDef::TBL_NAME_STORAGE,
            schema: ReferencesDef::DB_SCHEMA,
            comment: 'Склад',
        );
        $migrationService->addColumn(
            table: $storage,
            name: 'name',
            type: Types::TEXT,
            notNull: true,
            uniqueConstraint: true,
            comment: 'Название',
        );
        $migrationService->addColumn(
            table: $storage,
            name: 'city_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'Город ID',
            notNull: true,
        );
        $migrationService->addForeignKeyConstraint(
            table: $storage,
            foreignTable: $city,
            foreignTableSchema: null,
            localColumnNames: ['city_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
        $migrationService->addDeletedColumn($storage);
    }

    public function down(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.storage"
         */
        $migrationService->dropTable(
            name: ReferencesDef::TBL_NAME_STORAGE,
            schema: ReferencesDef::DB_SCHEMA,
        );

        /**
         * Table "public.city"
         */
        $migrationService->dropTable(
            name: ReferencesDef::TBL_NAME_CITY,
            schema: ReferencesDef::DB_SCHEMA,
        );
    }
}
