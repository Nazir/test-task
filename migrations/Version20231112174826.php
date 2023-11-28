<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DbDef;
use App\Modules\Product\Entity\ProductDef;
use App\Entity\References\ReferencesDef;
use App\Service\Common\MigrationService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20231112174826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tables "public.product"';
    }

    public function up(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.product"
         */
        $product = $migrationService->createTable(
            name: ProductDef::TBL_NAME_PRODUCT,
            schema: ProductDef::DB_SCHEMA,
            comment: 'Товар',
        );
        $migrationService->addColumn(
            table: $product,
            name: 'name',
            type: Types::TEXT,
            notNull: true,
            uniqueConstraint: true,
            comment: 'Название',
        );
        $migrationService->addColumn(
            table: $product,
            name: 'price',
            type: Types::DECIMAL,
            notNull: true,
            comment: 'Цена',
        );
        $migrationService->addColumn(
            table: $product,
            name: 'quantity_available_for_order',
            type: Types::INTEGER,
            notNull: true,
            comment: 'Кол-во доступное для заказа',
        );
        $migrationService->addColumn(
            table: $product,
            name: 'storage_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'Storage ID',
            notNull: true,
        );
        $migrationService->addForeignKeyConstraint(
            table: $product,
            foreignTable: ReferencesDef::TBL_NAME_STORAGE,
            foreignTableSchema: ReferencesDef::DB_SCHEMA,
            localColumnNames: ['storage_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
        $migrationService->addTimestampsColumns($product);
        $migrationService->addDeletedColumn($product);
    }

    public function down(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.product"
         */
        $migrationService->dropTable(
            name: ProductDef::TBL_NAME_PRODUCT,
            schema: ProductDef::DB_SCHEMA,
        );
    }
}
