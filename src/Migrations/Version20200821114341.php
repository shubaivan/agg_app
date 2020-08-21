<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200821114341 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE products ALTER name TYPE TEXT');
        $this->addSql('ALTER TABLE products ALTER name DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER group_identity TYPE TEXT');
        $this->addSql('ALTER TABLE products ALTER group_identity DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE products ALTER group_identity TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE products ALTER group_identity DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE products ALTER name DROP DEFAULT');
    }
}
