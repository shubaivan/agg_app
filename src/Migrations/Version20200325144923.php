<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325144923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE products ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE products ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE products ALTER product_url TYPE TEXT');
        $this->addSql('ALTER TABLE products ALTER product_url DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER image_url TYPE TEXT');
        $this->addSql('ALTER TABLE products ALTER image_url DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER tracking_url TYPE TEXT');
        $this->addSql('ALTER TABLE products ALTER tracking_url DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE products DROP created_at');
        $this->addSql('ALTER TABLE products DROP updated_at');
        $this->addSql('ALTER TABLE products ALTER product_url TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE products ALTER product_url DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER image_url TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE products ALTER image_url DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER tracking_url TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE products ALTER tracking_url DROP DEFAULT');
    }
}
