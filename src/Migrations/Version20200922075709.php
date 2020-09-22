<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200922075709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE manually_resource_job_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE manually_resource_job (
            id INT NOT NULL,
            created_at_admin_id INT DEFAULT NULL,
            shop_key VARCHAR(255) NOT NULL,
            url TEXT NOT NULL,
            dir_for_files VARCHAR(255) NOT NULL,
            redis_uniq_key VARCHAR(255) NOT NULL,
            status INT NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id))
         ');
        $this->addSql('CREATE INDEX IDX_B580B9358ED195F 
                ON manually_resource_job (created_at_admin_id)');
        $this->addSql('CREATE INDEX shop_key_idx ON manually_resource_job (shop_key)');
        $this->addSql('CREATE INDEX status_key_idx ON manually_resource_job (status)');
        $this->addSql('CREATE UNIQUE INDEX redis_uniq_key_uniq_idx ON manually_resource_job (redis_uniq_key)');
        $this->addSql('ALTER TABLE manually_resource_job 
            ADD CONSTRAINT FK_B580B9358ED195F 
            FOREIGN KEY (created_at_admin_id) 
            REFERENCES my_user (id) 
            NOT DEFERRABLE INITIALLY IMMEDIATE
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE manually_resource_job_id_seq CASCADE');
        $this->addSql('DROP TABLE manually_resource_job');
    }
}
