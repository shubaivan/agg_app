<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200527101028 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('drop index if exists ndsprc_custom_index');
        $this->addSql('drop index if exists ndsprc_swedish_custom_index');
        $this->addSql('drop index if exists ndsprc_swedish_custom_index_test');
        $this->addSql('drop index if exists ndsprc_swedish_custom_index_test_d');

        $this->addSql('
                create index npd_swedish_custom_index on products
                    using GIN(to_tsvector(\'pg_catalog.swedish\', name||price||description))
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists npd_swedish_custom_index');
    }
}
