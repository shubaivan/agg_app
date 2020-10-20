<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201012115756 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE files ADD brand_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_635405944F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_635405944F5D008 ON files (brand_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX brand_trgm_idx ON brand (brand_name)');
        $this->addSql('ALTER TABLE files DROP CONSTRAINT FK_635405944F5D008');
        $this->addSql('DROP INDEX IDX_635405944F5D008');
        $this->addSql('ALTER TABLE files DROP brand_id');
    }
}
