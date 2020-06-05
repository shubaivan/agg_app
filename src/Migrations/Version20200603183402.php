<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603183402 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE category_configurations DROP CONSTRAINT fk_e20c29e4197b9a54');
        $this->addSql('DROP INDEX idx_e20c29e4197b9a54');
        $this->addSql('ALTER TABLE category_configurations DROP main_category_name');
        $this->addSql('ALTER TABLE category_configurations DROP sub_key_words');
        $this->addSql('ALTER TABLE category_configurations DROP sub_category_name');
        $this->addSql('ALTER TABLE category_configurations RENAME COLUMN main_category_id_id TO category_id_id');
        $this->addSql('ALTER TABLE category_configurations ADD CONSTRAINT FK_E20C29E49777D11E FOREIGN KEY (category_id_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E20C29E49777D11E ON category_configurations (category_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE category_configurations DROP CONSTRAINT FK_E20C29E49777D11E');
        $this->addSql('DROP INDEX UNIQ_E20C29E49777D11E');
        $this->addSql('ALTER TABLE category_configurations ADD main_category_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category_configurations ADD sub_key_words TEXT NOT NULL');
        $this->addSql('ALTER TABLE category_configurations ADD sub_category_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE category_configurations RENAME COLUMN category_id_id TO main_category_id_id');
        $this->addSql('ALTER TABLE category_configurations ADD CONSTRAINT fk_e20c29e4197b9a54 FOREIGN KEY (main_category_id_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e20c29e4197b9a54 ON category_configurations (main_category_id_id)');
    }
}
