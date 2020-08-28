<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200819195855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE products DROP resource_identity_data');
        $this->addSql('ALTER TABLE products ALTER sku TYPE TEXT');
        $this->addSql('ALTER TABLE products ALTER sku DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER sku DROP NOT NULL');
        $this->addSql('ALTER TABLE products ADD identity_uniq_data TEXT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX identityUniqData_uniq_idx ON products (identity_uniq_data)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX identityUniqData_uniq_idx');
        $this->addSql('ALTER TABLE products ADD resource_identity_data VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE products ALTER sku TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE products ALTER sku DROP DEFAULT');
        $this->addSql('ALTER TABLE products ALTER sku SET NOT NULL');
        $this->addSql('ALTER TABLE products DROP identity_uniq_data');
    }
}
