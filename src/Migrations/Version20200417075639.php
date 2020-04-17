<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200417075639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create index sn_custom_index on shop
using GIN(to_tsvector(\'english\',coalesce(name,\'\')||\' \'))');
        $this->addSql('create index bn_custom_index on brand
using GIN(to_tsvector(\'english\',coalesce(name,\'\')||\' \'))');
        $this->addSql('create index cn_custom_index on category
using GIN(to_tsvector(\'english\',coalesce(name,\'\')||\' \'))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop index if exists sn_custom_index');
        $this->addSql('drop index if exists bn_custom_index');
        $this->addSql('drop index if exists cn_custom_index');
    }
}
