<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DbDef;
use App\Modules\Customer\Entity\CustomerDef;
use App\Modules\Order\Entity\OrderDef;
use App\Modules\Product\Entity\ProductDef;
use App\Service\Common\MigrationService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20231112175942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tables "public.order_status" & "public.order"';
    }

    public function up(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.order_status"
         */
        $orderStatus = $migrationService->createTable(
            name: OrderDef::TBL_NAME_ORDER_STATUS,
            schema: OrderDef::DB_SCHEMA,
            comment: 'Статус заявки',
        );
        $migrationService->addBaseSystemReferenceColumns(table: $orderStatus);

        /**
         * Table "public.order"
         */
        $order = $migrationService->createTable(
            name: OrderDef::TBL_NAME_ORDER,
            schema: OrderDef::DB_SCHEMA,
            comment: 'Заявка',
        );
        $migrationService->addColumn(
            table: $order,
            name: 'customer_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'Клиент (покупатель) ID',
            notNull: true,
        );
        $migrationService->addForeignKeyConstraint(
            table: $order,
            foreignTable: CustomerDef::TBL_NAME_CUSTOMER,
            foreignTableSchema: CustomerDef::DB_SCHEMA,
            localColumnNames: ['customer_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
        $migrationService->addColumn(
            table: $order,
            name: 'date',
            type: Types::DATE_MUTABLE,
            notNull: true,
            comment: 'Дата создания',
        );
        $migrationService->addColumn(
            table: $order,
            name: 'status_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'Статус заявки ID',
            notNull: true,
        );
        $migrationService->addForeignKeyConstraint(
            table: $order,
            foreignTable: $orderStatus,
            foreignTableSchema: null,
            localColumnNames: ['status_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
        $migrationService->addColumn(
            table: $order,
            name: 'tk',
            type: Types::TEXT,
            notNull: false,
            comment: 'Внешний трек-номер (ТК)',
        );
        $migrationService->addColumn(
            table: $order,
            name: 'delivery_price',
            type: Types::DECIMAL,
            notNull: false,
            comment: 'Цена доставки',
        );
        $migrationService->addTimestampsColumns($order);
        $migrationService->addAuxiliaryColumns($order);
        $migrationService->addDeletedColumn($order);

        /**
         * Table "public.order_product"
         */
        $orderProduct = $migrationService->createTable(
            name: OrderDef::TBL_NAME_ORDER_PRODUCT,
            schema: OrderDef::DB_SCHEMA,
            comment: 'Заявка - Товар',
            withId: false,
        );
        $migrationService->addColumn(
            table: $orderProduct,
            name: 'order_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'Заявка ID',
            notNull: true,
        );
        $migrationService->addForeignKeyConstraint(
            table: $orderProduct,
            foreignTable: $order,
            foreignTableSchema: null,
            localColumnNames: ['order_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
        $migrationService->addColumn(
            table: $orderProduct,
            name: 'product_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'Товар ID',
            notNull: true,
        );
        $migrationService->addForeignKeyConstraint(
            table: $orderProduct,
            foreignTable: ProductDef::TBL_NAME_PRODUCT,
            foreignTableSchema: ProductDef::DB_SCHEMA,
            localColumnNames: ['product_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
        $migrationService->setPrimaryKey(table: $orderProduct, columnNames: ['order_id', 'product_id']);
        $migrationService->addColumn(
            table: $orderProduct,
            name: 'product_quantity',
            type: Types::INTEGER,
            notNull: true,
            comment: 'Количество товара',
        );
    }

    public function down(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.order_product"
         */
        $migrationService->dropTable(
            name: OrderDef::TBL_NAME_ORDER_PRODUCT,
            schema: OrderDef::DB_SCHEMA,
        );

        /**
         * Table "public.order"
         */
        $migrationService->dropTable(
            name: OrderDef::TBL_NAME_ORDER,
            schema: OrderDef::DB_SCHEMA,
        );

        /**
         * Table "public.order_status"
         */
        $migrationService->dropTable(
            name: OrderDef::TBL_NAME_ORDER_STATUS,
            schema: OrderDef::DB_SCHEMA,
        );
    }
}
