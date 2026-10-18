<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\ForeignKeyConstraint\ReferentialAction;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Index\IndexType;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * TasteCollection module — initial schema.
 *
 * Foreign keys only between this module's own tables.
 * taste_judgments.user_id deliberately has no FK: users belong to Identity.
 *
 * Built with DBAL's editor API (see the Identity migration for why).
 */
final class Version20261018100100TasteCollection extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[TasteCollection] Create preparations, ingredients, ingredient_variants, taste_judgments';
    }

    public function up(Schema $schema): void
    {
        // Preparation tree: raw / cooked → grilled, steamed, boiled…
        $preparations = Table::editor()
            ->setUnquotedName('preparations')
            ->setColumns(
                self::column('id', Types::GUID),
                self::column('name', Types::STRING, length: 100),
                self::column('parent_id', Types::GUID, nullable: true),
            )
            ->setPrimaryKeyConstraint(self::primaryKey('id'))
            ->setIndexes(
                self::index('uniq_preparations_name', IndexType::UNIQUE, 'name'),
                self::index('idx_preparations_parent', IndexType::REGULAR, 'parent_id'),
            )
            ->setForeignKeyConstraints(
                self::foreignKey('fk_preparations_parent', 'parent_id', 'preparations'),
            )
            ->create();

        $ingredients = Table::editor()
            ->setUnquotedName('ingredients')
            ->setColumns(
                self::column('id', Types::GUID),
                self::column('name', Types::STRING, length: 150),
                self::column('category', Types::STRING, length: 50),
                self::column('region', Types::STRING, length: 50),
                self::column('commonness', Types::FLOAT), // 0–1
                self::column('default_preparation_id', Types::GUID),
                self::column('created_at', Types::DATETIME_IMMUTABLE),
            )
            ->setPrimaryKeyConstraint(self::primaryKey('id'))
            ->setIndexes(
                self::index('uniq_ingredients_name', IndexType::UNIQUE, 'name'),
                self::index('idx_ingredients_default_preparation', IndexType::REGULAR, 'default_preparation_id'),
            )
            ->setForeignKeyConstraints(
                self::foreignKey('fk_ingredients_default_preparation', 'default_preparation_id', 'preparations'),
            )
            ->create();

        // A variant = ingredient + preparation. MVP seeds only default variants.
        $variants = Table::editor()
            ->setUnquotedName('ingredient_variants')
            ->setColumns(
                self::column('id', Types::GUID),
                self::column('ingredient_id', Types::GUID),
                self::column('preparation_id', Types::GUID),
            )
            ->setPrimaryKeyConstraint(self::primaryKey('id'))
            ->setIndexes(
                self::index('uniq_variants_ingredient_preparation', IndexType::UNIQUE, 'ingredient_id', 'preparation_id'),
                self::index('idx_variants_ingredient', IndexType::REGULAR, 'ingredient_id'),
                self::index('idx_variants_preparation', IndexType::REGULAR, 'preparation_id'),
            )
            ->setForeignKeyConstraints(
                self::foreignKey('fk_variants_ingredient', 'ingredient_id', 'ingredients', ReferentialAction::CASCADE),
                self::foreignKey('fk_variants_preparation', 'preparation_id', 'preparations'),
            )
            ->create();

        // Append-only. Exactly one of liked / score is set (enforced by the TasteJudgment entity).
        $judgments = Table::editor()
            ->setUnquotedName('taste_judgments')
            ->setColumns(
                self::column('id', Types::GUID),
                self::column('user_id', Types::GUID),
                self::column('variant_id', Types::GUID),
                self::column('liked', Types::BOOLEAN, nullable: true),
                self::column('score', Types::SMALLINT, nullable: true), // 0–10
                self::column('created_at', Types::DATETIME_IMMUTABLE),
            )
            ->setPrimaryKeyConstraint(self::primaryKey('id'))
            ->setIndexes(
                // "Latest judgment per user + variant" lookups.
                self::index('idx_judgments_user_variant_created', IndexType::REGULAR, 'user_id', 'variant_id', 'created_at'),
                self::index('idx_judgments_variant', IndexType::REGULAR, 'variant_id'),
            )
            ->create();

        foreach ($this->platform->getCreateTablesSQL([$preparations, $ingredients, $variants, $judgments]) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        // Children first.
        foreach (['taste_judgments', 'ingredient_variants', 'ingredients', 'preparations'] as $table) {
            $this->addSql($this->platform->getDropTableSQL($table));
        }
    }

    private static function column(string $name, string $type, ?int $length = null, bool $nullable = false): Column
    {
        return Column::editor()
            ->setUnquotedName($name)
            ->setTypeName($type)
            ->setLength($length)
            ->setNotNull(!$nullable)
            ->create();
    }

    private static function primaryKey(string $column): PrimaryKeyConstraint
    {
        return PrimaryKeyConstraint::editor()->setUnquotedColumnNames($column)->create();
    }

    private static function index(string $name, IndexType $type, string $firstColumn, string ...$otherColumns): Index
    {
        return Index::editor()
            ->setUnquotedName($name)
            ->setType($type)
            ->setUnquotedColumnNames($firstColumn, ...$otherColumns)
            ->create();
    }

    private static function foreignKey(
        string $name,
        string $column,
        string $referencedTable,
        ReferentialAction $onDelete = ReferentialAction::NO_ACTION,
    ): ForeignKeyConstraint {
        return ForeignKeyConstraint::editor()
            ->setUnquotedName($name)
            ->setUnquotedReferencingColumnNames($column)
            ->setUnquotedReferencedTableName($referencedTable)
            ->setUnquotedReferencedColumnNames('id')
            ->setOnDeleteAction($onDelete)
            ->create();
    }
}
