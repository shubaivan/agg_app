<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029124808 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE brand_shop_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE brand_shop (id INT NOT NULL, brand_id INT DEFAULT NULL, shop_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8B0D461D44F5D008 ON brand_shop (brand_id)');
        $this->addSql('CREATE INDEX IDX_8B0D461D4D16C4DD ON brand_shop (shop_id)');
        $this->addSql('ALTER TABLE brand_shop ADD CONSTRAINT FK_8B0D461D44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand_shop ADD CONSTRAINT FK_8B0D461D4D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE brand_shop_id_seq CASCADE');
        $this->addSql('DROP TABLE brand_shop');
    }
}
