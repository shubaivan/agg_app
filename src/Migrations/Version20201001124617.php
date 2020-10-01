<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201001124617 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE shop ADD seo_title TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE shop ADD seo_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE shop ADD seo_text1 TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE shop ADD seo_text2 TEXT DEFAULT NULL');

        $this->addSql('ALTER TABLE category ADD seo_title TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD seo_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD seo_text1 TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD seo_text2 TEXT DEFAULT NULL');

        $this->addSql('ALTER TABLE brand ADD seo_title TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD seo_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD seo_text1 TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD seo_text2 TEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE category DROP seo_title');
        $this->addSql('ALTER TABLE category DROP seo_description');
        $this->addSql('ALTER TABLE category DROP seo_text1');
        $this->addSql('ALTER TABLE category DROP seo_text2');

        $this->addSql('ALTER TABLE brand DROP seo_title');
        $this->addSql('ALTER TABLE brand DROP seo_description');
        $this->addSql('ALTER TABLE brand DROP seo_text1');
        $this->addSql('ALTER TABLE brand DROP seo_text2');

        $this->addSql('ALTER TABLE shop DROP seo_title');
        $this->addSql('ALTER TABLE shop DROP seo_description');
        $this->addSql('ALTER TABLE shop DROP seo_text1');
        $this->addSql('ALTER TABLE shop DROP seo_text2');
    }
}
