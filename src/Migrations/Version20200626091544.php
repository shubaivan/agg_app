<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200626091544 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE category_configurations ADD negative_key_words_fts tsvector DEFAULT NULL');
        $this->addSql('DROP TRIGGER IF EXISTS tsvectorupdate ON category_configurations');
        $this->addSql('DROP FUNCTION IF EXISTS category_configurations_ts_trigger');
        $this->addSql('
        CREATE FUNCTION category_configurations_ts_trigger() RETURNS trigger AS $$
begin
  new.common_fts :=
     setweight(to_tsvector(\'my_swedish\', coalesce(new.key_words,\'\')), \'A\');
  new.negative_key_words_fts :=
     setweight(to_tsvector(\'my_swedish\', coalesce(new.negative_key_words,\'\')), \'A\');        	 
  return new;
end
$$ LANGUAGE plpgsql
        ');
        $this->addSql('
        CREATE TRIGGER tsvectorupdate BEFORE INSERT OR UPDATE
    ON category_configurations FOR EACH ROW EXECUTE FUNCTION category_configurations_ts_trigger()
        ');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TRIGGER IF EXISTS tsvectorupdate ON category_configurations');
        $this->addSql('DROP FUNCTION IF EXISTS category_configurations_ts_trigger');
        $this->addSql('ALTER TABLE category_configurations DROP negative_key_words_fts');
    }
}
