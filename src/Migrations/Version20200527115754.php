<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527115754 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('drop index if exists brand_trgm_idx');
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        $this->addSql('CREATE INDEX brand_trgm_idx ON brand USING GIN (brand_name gin_trgm_ops)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists brand_trgm_idx');
    }
}
