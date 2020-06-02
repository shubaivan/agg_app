<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200529104547 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE INDEX IF NOT EXISTS created_desc_index ON products (created_at DESC NULLS LAST)');
        $this->addSql('CREATE INDEX IF NOT EXISTS created_asc_index ON products (created_at ASC NULLS LAST)');
        $this->addSql('CREATE INDEX IF NOT EXISTS price_desc_index ON products (price DESC NULLS LAST)');
        $this->addSql('CREATE INDEX IF NOT EXISTS price_asc_index ON products (price ASC NULLS LAST)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists created_desc_index');
        $this->addSql('drop index if exists created_asc_index');
        $this->addSql('drop index if exists price_desc_index');
        $this->addSql('drop index if exists price_asc_index');
    }
}
