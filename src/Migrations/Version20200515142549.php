<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200515142549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('drop index if exists sn_custom_index');
        $this->addSql('drop index if exists sn_swedish_custom_index');
        $this->addSql('create index sn_custom_index on shop
using GIN(to_tsvector(\'english\',coalesce(shop_name,\'\')||\' \'))');
        $this->addSql('create index sn_swedish_custom_index on shop
using GIN(to_tsvector(\'pg_catalog.swedish\',coalesce(shop_name,\'\')||\' \'))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
