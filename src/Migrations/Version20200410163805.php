<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410163805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE user_ip_product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE user_ip_product DROP CONSTRAINT fk_5b9c784c42fda5c7');
        $this->addSql('ALTER TABLE user_ip_product DROP CONSTRAINT fk_5b9c784c4584665a');
        $this->addSql('DROP INDEX IF EXISTS idx_5b9c784c42fda5c7');
        $this->addSql('DROP INDEX IF EXISTS idx_5b9c784c4584665a');
        $this->addSql('DROP INDEX IF EXISTS "primary"');
        $this->addSql('ALTER TABLE user_ip_product ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE user_ip_product ADD products_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_ip_product ADD ips_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_ip_product ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE user_ip_product ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE user_ip_product DROP user_ip_id');
        $this->addSql('ALTER TABLE user_ip_product DROP product_id');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT FK_5B9C784C6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT FK_5B9C784C87610CAE FOREIGN KEY (ips_id) REFERENCES user_ip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5B9C784C6C8A81A9 ON user_ip_product (products_id)');
        $this->addSql('CREATE INDEX IDX_5B9C784C87610CAE ON user_ip_product (ips_id)');
        $this->addSql('ALTER TABLE user_ip_product ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_ip_product_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_ip_product DROP CONSTRAINT FK_5B9C784C6C8A81A9');
        $this->addSql('ALTER TABLE user_ip_product DROP CONSTRAINT FK_5B9C784C87610CAE');
        $this->addSql('DROP INDEX IDX_5B9C784C6C8A81A9');
        $this->addSql('DROP INDEX IDX_5B9C784C87610CAE');
        $this->addSql('DROP INDEX user_ip_product_pkey');
        $this->addSql('ALTER TABLE user_ip_product ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_ip_product DROP products_id');
        $this->addSql('ALTER TABLE user_ip_product DROP ips_id');
        $this->addSql('ALTER TABLE user_ip_product DROP created_at');
        $this->addSql('ALTER TABLE user_ip_product DROP updated_at');
        $this->addSql('ALTER TABLE user_ip_product RENAME COLUMN id TO user_ip_id');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT fk_5b9c784c42fda5c7 FOREIGN KEY (user_ip_id) REFERENCES user_ip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT fk_5b9c784c4584665a FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5b9c784c42fda5c7 ON user_ip_product (user_ip_id)');
        $this->addSql('CREATE INDEX idx_5b9c784c4584665a ON user_ip_product (product_id)');
        $this->addSql('ALTER TABLE user_ip_product ADD PRIMARY KEY (user_ip_id, product_id)');
    }
}
