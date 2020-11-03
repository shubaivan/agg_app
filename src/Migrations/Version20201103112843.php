<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201103112843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE brand_strategy ALTER brand_id SET NOT NULL');
        $this->addSql('ALTER TABLE brand_strategy ALTER strategy_id SET NOT NULL');
        $this->addSql('ALTER TABLE brand_strategy ALTER shop_id SET NOT NULL');
        $this->addSql('ALTER TABLE brand_strategy ALTER required_args SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE brand_strategy ALTER brand_id DROP NOT NULL');
        $this->addSql('ALTER TABLE brand_strategy ALTER strategy_id DROP NOT NULL');
        $this->addSql('ALTER TABLE brand_strategy ALTER shop_id DROP NOT NULL');
        $this->addSql('ALTER TABLE brand_strategy ALTER required_args DROP NOT NULL');
    }
}
