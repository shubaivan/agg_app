<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200528082340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('drop index if exists products_extras_idx');

        $this->addSql('create index on products using gin(extras);');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index if exists products_extras_idx');
    }
}
