<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200923094101 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE shop ADD slug TEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX shop_slug_index ON shop (slug)');

        $this->addSql('ALTER TABLE manually_resource_job ALTER status SET DEFAULT 0');

        $this->addSql('ALTER TABLE products ADD slug TEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX product_slug_index ON products (slug)');

        $this->addSql('ALTER TABLE category ADD slug TEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX category_slug_index ON category (slug)');

        $this->addSql('ALTER INDEX slug_index RENAME TO brand_slug_index');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX product_slug_index');
        $this->addSql('ALTER TABLE products DROP slug');

        $this->addSql('DROP INDEX category_slug_index');
        $this->addSql('ALTER TABLE category DROP slug');

        $this->addSql('ALTER INDEX brand_slug_index RENAME TO slug_index');

        $this->addSql('DROP INDEX shop_slug_index');
        $this->addSql('ALTER TABLE shop DROP slug');

        $this->addSql('ALTER TABLE manually_resource_job ALTER status DROP DEFAULT');
    }
}
