<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200616080220 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('drop index if exists kw_my_swedish_custom_index');

        $this->addSql('
                create index kw_my_swedish_custom_index on category_configurations
                    using GIN(to_tsvector(\'my_swedish\', key_words))
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists kw_my_swedish_custom_index');
    }
}
