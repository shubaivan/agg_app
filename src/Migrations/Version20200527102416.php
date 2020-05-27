<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527102416 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('drop index if exists cn_custom_index');
        $this->addSql('drop index if exists cn_swedish_custom_index');

        $this->addSql('
                create index cn_swedish_custom_index on category
                    using GIN(to_tsvector(\'pg_catalog.swedish\', category_name))
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists cn_swedish_custom_index');
    }
}
