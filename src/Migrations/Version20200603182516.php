<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603182516 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE category_configurations ADD main_category_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category_configurations ADD CONSTRAINT FK_E20C29E4197B9A54 FOREIGN KEY (main_category_id_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E20C29E4197B9A54 ON category_configurations (main_category_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE category_configurations DROP CONSTRAINT FK_E20C29E4197B9A54');
        $this->addSql('DROP INDEX IDX_E20C29E4197B9A54');
        $this->addSql('ALTER TABLE category_configurations DROP main_category_id_id');
    }
}
