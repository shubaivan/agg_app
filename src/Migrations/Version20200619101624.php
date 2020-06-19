<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200619101624 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE INDEX common_ndpb_search_idx ON products USING GIN (common_fts);
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists common_ndpb_search_idx');
    }
}
