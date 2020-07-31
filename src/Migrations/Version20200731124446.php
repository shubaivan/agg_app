<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200731124446 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE products ADD product_short_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD product_model TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD model_number TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD delivery_restrictions TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD basket_link TEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        
        $this->addSql('ALTER TABLE products DROP product_short_description');
        $this->addSql('ALTER TABLE products DROP product_model');
        $this->addSql('ALTER TABLE products DROP model_number');
        $this->addSql('ALTER TABLE products DROP delivery_restrictions');
        $this->addSql('ALTER TABLE products DROP basket_link');
    }
}
