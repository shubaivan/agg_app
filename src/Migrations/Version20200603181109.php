<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200603181109 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE category_sub_categories (category_id INT NOT NULL, sub_category_id INT NOT NULL, PRIMARY KEY(category_id, sub_category_id))');
        $this->addSql('CREATE INDEX IDX_F728969812469DE2 ON category_sub_categories (category_id)');
        $this->addSql('CREATE INDEX IDX_F7289698F7BFE87C ON category_sub_categories (sub_category_id)');
        $this->addSql('ALTER TABLE category_sub_categories ADD CONSTRAINT FK_F728969812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_sub_categories ADD CONSTRAINT FK_F7289698F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE category_sub_categories');
    }
}
