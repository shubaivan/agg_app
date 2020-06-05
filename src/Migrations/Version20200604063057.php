<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200604063057 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE category_relations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category_relations (id INT NOT NULL, sub_category_id INT DEFAULT NULL, main_category_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D30C6D48F7BFE87C ON category_relations (sub_category_id)');
        $this->addSql('CREATE INDEX IDX_D30C6D48C6C55574 ON category_relations (main_category_id)');
        $this->addSql('ALTER TABLE category_relations ADD CONSTRAINT FK_D30C6D48F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_relations ADD CONSTRAINT FK_D30C6D48C6C55574 FOREIGN KEY (main_category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE category_sub_categories');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE category_relations_id_seq CASCADE');
        $this->addSql('CREATE TABLE category_sub_categories (category_id INT NOT NULL, sub_category_id INT NOT NULL, PRIMARY KEY(category_id, sub_category_id))');
        $this->addSql('CREATE INDEX idx_f7289698f7bfe87c ON category_sub_categories (sub_category_id)');
        $this->addSql('CREATE INDEX idx_f728969812469de2 ON category_sub_categories (category_id)');
        $this->addSql('ALTER TABLE category_sub_categories ADD CONSTRAINT fk_f728969812469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_sub_categories ADD CONSTRAINT fk_f7289698f7bfe87c FOREIGN KEY (sub_category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE category_relations');
    }
}
