<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201027170734 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE brand_strategy_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE brand_strategy (id INT NOT NULL, brand_id INT DEFAULT NULL, strategy_id INT DEFAULT NULL, required_args jsonb DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D376D6FC44F5D008 ON brand_strategy (brand_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D376D6FCD5CAD932 ON brand_strategy (strategy_id)');
        $this->addSql('ALTER TABLE brand_strategy ADD CONSTRAINT FK_D376D6FC44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE brand_strategy ADD CONSTRAINT FK_D376D6FCD5CAD932 FOREIGN KEY (strategy_id) REFERENCES strategies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE brand DROP CONSTRAINT fk_1c52f958d5cad932');
        $this->addSql('DROP INDEX idx_1c52f958d5cad932');
        $this->addSql('ALTER TABLE brand DROP strategy_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE brand_strategy_id_seq CASCADE');
        $this->addSql('DROP TABLE brand_strategy');

        $this->addSql('ALTER TABLE brand ADD strategy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD CONSTRAINT fk_1c52f958d5cad932 FOREIGN KEY (strategy_id) REFERENCES strategies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1c52f958d5cad932 ON brand (strategy_id)');
    }
}
