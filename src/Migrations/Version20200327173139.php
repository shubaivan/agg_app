<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200327173139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE products ADD original_price NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE category ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE category ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE brand ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE brand ALTER updated_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE products DROP original_price');
        $this->addSql('ALTER TABLE brand ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE brand ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE category ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE category ALTER updated_at DROP NOT NULL');
    }
}
