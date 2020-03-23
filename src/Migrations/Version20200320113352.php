<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200320113352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE products_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE products (id INT NOT NULL, sku VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, category VARCHAR(255) DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, shipping VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, instock VARCHAR(255) DEFAULT NULL, product_url VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, tracking_url VARCHAR(255) DEFAULT NULL, brand VARCHAR(255) DEFAULT NULL, original_price VARCHAR(255) DEFAULT NULL, ean VARCHAR(255) DEFAULT NULL, manufacturer_article_number VARCHAR(255) DEFAULT NULL, extras VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX sku_idx ON products (sku)');
        $this->addSql('DROP TABLE product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE products_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, article VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE products');
    }
}
