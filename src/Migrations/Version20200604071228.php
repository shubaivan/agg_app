<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200604071228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('drop index if exists kw_swedish_custom_index');

        $this->addSql('
                create index kw_swedish_custom_index on category_configurations
                    using GIN(to_tsvector(\'pg_catalog.swedish\', key_words))
        ');
        $this->addSql('CREATE INDEX kw_trgm_idx ON category_configurations USING GIN (key_words gin_trgm_ops)');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists kw_swedish_custom_index');
        $this->addSql('drop index if exists kw_trgm_idx');

    }
}
