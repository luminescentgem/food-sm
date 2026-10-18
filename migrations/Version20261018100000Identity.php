<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Identity module — initial schema.
 *
 * Tables are built with DBAL's editor API and turned into SQL by the platform:
 * the mutable Schema API (createTable(), addColumn()…) is deprecated since DBAL 4.5,
 * and doctrine/migrations 3.x still hands migrations a mutable Schema.
 */
final class Version20261018100000Identity extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Identity] Create users table';
    }

    public function up(Schema $schema): void
    {
        $users = Table::editor()
            ->setUnquotedName('users')
            ->setColumns(
                Column::editor()->setUnquotedName('id')->setTypeName(Types::GUID)->create(),
                Column::editor()->setUnquotedName('name')->setTypeName(Types::STRING)->setLength(100)->create(),
                Column::editor()->setUnquotedName('created_at')->setTypeName(Types::DATETIME_IMMUTABLE)->create(),
            )
            ->setPrimaryKeyConstraint(PrimaryKeyConstraint::editor()->setUnquotedColumnNames('id')->create())
            ->create();

        foreach ($this->platform->getCreateTablesSQL([$users]) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql($this->platform->getDropTableSQL('users'));
    }
}
