<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DbDef;
use App\Entity\References\ReferencesDef;
use App\Modules\Customer\Entity\CustomerDef;
use App\Service\Common\MigrationService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20231112170129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tables "public.customer"';
    }

    public function up(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.customer"
         */
        $customer = $migrationService->createTable(
            name: CustomerDef::TBL_NAME_CUSTOMER,
            schema: CustomerDef::DB_SCHEMA,
            comment: 'Клиент (покупатель)',
        );
        $migrationService->addColumn(
            table: $customer,
            name: 'name',
            type: Types::TEXT,
            notNull: true,
            // uniqueConstraint: true,
            comment: 'ФИО',
        );
        $migrationService->addColumn(
            table: $customer,
            name: 'phone',
            type: Types::TEXT,
            notNull: true,
            uniqueConstraint: true,
            comment: 'Номер телефона',
        );
        $migrationService->addColumn(
            table: $customer,
            name: 'email',
            type: Types::TEXT,
            notNull: true,
            uniqueConstraint: true,
            comment: 'Email',
        );
        $migrationService->addColumn(
            table: $customer,
            name: 'city_id',
            type: DbDef::TBL_COL_ID_TYPE,
            comment: 'Адрес доставки - Город ID',
            notNull: true,
        );
        $migrationService->addForeignKeyConstraint(
            table: $customer,
            foreignTable: ReferencesDef::TBL_NAME_CITY,
            foreignTableSchema: ReferencesDef::DB_SCHEMA,
            localColumnNames: ['city_id'],
            foreignColumnNames: [DbDef::TBL_COL_ID_NAME],
        );
        $migrationService->addColumn(
            table: $customer,
            name: 'street',
            type: Types::TEXT,
            notNull: true,
            comment: 'Адрес доставки - Улица',
        );
        $migrationService->addColumn(
            table: $customer,
            name: 'house_number',
            type: Types::TEXT,
            notNull: true,
            comment: 'Адрес доставки - Номер дома',
        );
        $migrationService->addColumn(
            table: $customer,
            name: 'apartment_number',
            type: Types::TEXT,
            notNull: false,
            comment: 'Адрес доставки - Квартира',
        );
        $migrationService->addTimestampsColumns($customer);
        $migrationService->addDeletedColumn($customer);
    }

    public function down(Schema $schema): void
    {
        $migrationService = new MigrationService($schema, $this->platform);

        /**
         * Table "public.customer"
         */
        $migrationService->dropTable(
            name: CustomerDef::TBL_NAME_CUSTOMER,
            schema: CustomerDef::DB_SCHEMA,
        );
    }
}
