<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527102523 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('drop index if exists bn_custom_index');
        $this->addSql('drop index if exists bn_swedish_custom_index');

        $this->addSql('
                create index bn_custom_index on brand
                    using GIN(to_tsvector(\'pg_catalog.swedish\', brand_name))
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists bn_custom_index');
    }
}
