<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200402204204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE product_shop');
        $this->addSql('ALTER TABLE products ADD shop_relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AE021674B FOREIGN KEY (shop_relation_id) REFERENCES shop (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B3BA5A5AE021674B ON products (shop_relation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE product_shop (product_id INT NOT NULL, shop_id INT NOT NULL, PRIMARY KEY(product_id, shop_id))');
        $this->addSql('CREATE INDEX idx_21826e034584665a ON product_shop (product_id)');
        $this->addSql('CREATE INDEX idx_21826e034d16c4dd ON product_shop (shop_id)');
        $this->addSql('ALTER TABLE product_shop ADD CONSTRAINT fk_21826e034584665a FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_shop ADD CONSTRAINT fk_21826e034d16c4dd FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5AE021674B');
        $this->addSql('DROP INDEX IDX_B3BA5A5AE021674B');
        $this->addSql('ALTER TABLE products DROP shop_relation_id');
    }
}
