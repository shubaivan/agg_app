<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201020090414 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE files ADD shop_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE files ADD CONSTRAINT FK_63540594D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_63540594D16C4DD ON files (shop_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE files DROP CONSTRAINT FK_63540594D16C4DD');
        $this->addSql('DROP INDEX IDX_63540594D16C4DD');
        $this->addSql('ALTER TABLE files DROP shop_id');
    }
}
