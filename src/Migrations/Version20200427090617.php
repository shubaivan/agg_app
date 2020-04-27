<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427090617 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('create index cn_swedish_custom_index on category
using GIN(to_tsvector(\'pg_catalog.swedish\',coalesce(category_name,\'\')||\' \'))');

        $this->addSql('create index bn_swedish_custom_index on brand
using GIN(to_tsvector(\'pg_catalog.swedish\',coalesce(name,\'\')||\' \'))');

        $this->addSql('create index sn_swedish_custom_index on shop
using GIN(to_tsvector(\'pg_catalog.swedish\',coalesce(name,\'\')||\' \'))');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists cn_swedish_custom_index');
        $this->addSql('drop index if exists bn_swedish_custom_index');
        $this->addSql('drop index if exists sn_swedish_custom_index');
    }
}
