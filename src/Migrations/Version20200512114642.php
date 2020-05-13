<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200512114642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('drop index if exists ndsprc_swedish_custom_index');


        $this->addSql('create index ndsprc_swedish_custom_index on products
using GIN(to_tsvector(\'pg_catalog.swedish\',coalesce(name,\'\')||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')||\' \'||coalesce(shop,\'\')))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop index if exists ndsprc_swedish_custom_index');

        $this->addSql('create index ndsprc_swedish_custom_index on products
using GIN(to_tsvector(\'pg_catalog.swedish\',coalesce(name,\'\')||\' \'||coalesce(description,\'\')||\' \'||coalesce(sku,\'\')||\' \'||coalesce(price,0)||\' \'||coalesce(category,\'\')||\' \'||coalesce(brand,\'\')))');
    }
}
