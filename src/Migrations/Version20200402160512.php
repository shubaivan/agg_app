<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200402160512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE shop_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_ip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE shop (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX shop_name_idx ON shop (name)');
        $this->addSql('CREATE TABLE shop_product (shop_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(shop_id, product_id))');
        $this->addSql('CREATE INDEX IDX_D07944874D16C4DD ON shop_product (shop_id)');
        $this->addSql('CREATE INDEX IDX_D07944874584665A ON shop_product (product_id)');
        $this->addSql('CREATE TABLE user_ip (id INT NOT NULL, ip VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_ip_product (user_ip_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(user_ip_id, product_id))');
        $this->addSql('CREATE INDEX IDX_5B9C784C42FDA5C7 ON user_ip_product (user_ip_id)');
        $this->addSql('CREATE INDEX IDX_5B9C784C4584665A ON user_ip_product (product_id)');
        $this->addSql('ALTER TABLE shop_product ADD CONSTRAINT FK_D07944874D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shop_product ADD CONSTRAINT FK_D07944874584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT FK_5B9C784C42FDA5C7 FOREIGN KEY (user_ip_id) REFERENCES user_ip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT FK_5B9C784C4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD shop VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shop_product DROP CONSTRAINT FK_D07944874D16C4DD');
        $this->addSql('ALTER TABLE user_ip_product DROP CONSTRAINT FK_5B9C784C42FDA5C7');
        $this->addSql('DROP SEQUENCE shop_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_ip_id_seq CASCADE');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE shop_product');
        $this->addSql('DROP TABLE user_ip');
        $this->addSql('DROP TABLE user_ip_product');
        $this->addSql('ALTER TABLE products DROP shop');
    }
}
