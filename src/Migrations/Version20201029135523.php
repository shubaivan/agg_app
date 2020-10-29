<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029135523 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE brand_shop ADD brand_slug TEXT NOT NULL');
        $this->addSql('ALTER TABLE brand_shop ADD shop_slug TEXT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX brand_shop_uniq_slugs ON brand_shop (brand_slug, shop_slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX brand_shop_uniq_slugs');
        $this->addSql('ALTER TABLE brand_shop DROP brand_slug');
        $this->addSql('ALTER TABLE brand_shop DROP shop_slug');
    }
}
