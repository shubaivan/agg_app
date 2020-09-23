<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200910154039 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE category ADD position INT DEFAULT 0');

        $this->addSql('CREATE INDEX IF NOT EXISTS position_desc_index ON category (position DESC NULLS LAST)');
        $this->addSql('CREATE INDEX IF NOT EXISTS position_asc_index ON category (position ASC NULLS LAST)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('drop index if exists position_desc_index');
        $this->addSql('drop index if exists position_asc_index');

        $this->addSql('ALTER TABLE category DROP position');
    }
}
