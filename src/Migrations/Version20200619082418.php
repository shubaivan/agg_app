<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200619082418 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE products ADD common_fts tsvector NOT NULL');
        $this->addSql('DROP TRIGGER IF EXISTS tsvectorupdate ON products');
        $this->addSql('DROP FUNCTION IF EXISTS products_ts_trigger');
        $this->addSql('
        CREATE FUNCTION products_ts_trigger() RETURNS trigger AS $$
begin
  new.common_fts :=
     setweight(to_tsvector(\'pg_catalog.swedish\', coalesce(new.name,\'\')), \'A\') ||	 
     setweight(to_tsvector(\'pg_catalog.swedish\', coalesce(new.description,\'\')), \'B\') ||
		 setweight(to_tsvector(\'pg_catalog.swedish\', coalesce(new.price::text,\'\')), \'C\') ||
		 setweight(to_tsvector(\'pg_catalog.swedish\', coalesce(new.brand,\'\')), \'D\');
  return new;
end
$$ LANGUAGE plpgsql
        ');
        $this->addSql('
        CREATE TRIGGER tsvectorupdate BEFORE INSERT OR UPDATE
    ON products FOR EACH ROW EXECUTE FUNCTION products_ts_trigger()
        ');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TRIGGER IF EXISTS tsvectorupdate ON products');
        $this->addSql('DROP FUNCTION IF EXISTS products_ts_trigger');
        $this->addSql('ALTER TABLE products DROP common_fts');
    }
}
