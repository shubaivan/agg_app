<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201029122314 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_ip_product DROP CONSTRAINT FK_5B9C784C6C8A81A9');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT FK_5B9C784C6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE user_ip_product DROP CONSTRAINT fk_5b9c784c6c8a81a9');
        $this->addSql('ALTER TABLE user_ip_product ADD CONSTRAINT fk_5b9c784c6c8a81a9 FOREIGN KEY (products_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
