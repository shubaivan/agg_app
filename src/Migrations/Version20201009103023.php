<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201009103023 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX IF EXISTS category_name_idx');
        $this->addSql('ALTER TABLE category ADD slug_for_match TEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX category_slug_for_match_index ON category (slug_for_match)');
        $this->addSql('CREATE INDEX category_name_index ON category (category_name)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX IF EXISTS category_name_idx');
        $this->addSql('DROP INDEX IF EXISTS category_slug_for_match_index');
        $this->addSql('ALTER TABLE category DROP slug_for_match');
        $this->addSql('CREATE UNIQUE INDEX category_name_idx ON category (category_name)');
    }
}
