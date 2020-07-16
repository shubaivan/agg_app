<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200715074432 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE admin_configuration ADD data_fts tsvector NOT NULL');
        $this->addSql('DROP TRIGGER IF EXISTS tsvectorupdate ON admin_configuration');
        $this->addSql('DROP FUNCTION IF EXISTS admin_configuration_ts_trigger');
        $this->addSql('
        CREATE FUNCTION admin_configuration_ts_trigger() RETURNS trigger AS $$
begin
    IF (new.property_name = \'GLOBAL_NEGATIVE_BRAND_KEY_WORDS\') THEN
        new.data_fts :=
         setweight(to_tsvector(\'my_swedish\', coalesce(regexp_replace(regexp_replace(new.property_data, \' |\.|!|:|"|\'\'|&\', \'-\', \'g\'), \'-+\', \'-\', \'g\'),\'\')), \'A\');           	
        return new;
    
    ELSE
    
    new.data_fts :=
     setweight(to_tsvector(\'my_swedish\', coalesce(new.property_data,\'\')), \'A\');   	 
    return new;
                    
    END IF; 
end
$$ LANGUAGE plpgsql
        ');
        $this->addSql('
        CREATE TRIGGER tsvectorupdate BEFORE INSERT OR UPDATE
    ON admin_configuration FOR EACH ROW EXECUTE FUNCTION admin_configuration_ts_trigger()
        ');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TRIGGER IF EXISTS tsvectorupdate ON admin_configuration_ts_trigger');
        $this->addSql('DROP FUNCTION IF EXISTS admin_configuration_ts_trigger');
        $this->addSql('ALTER TABLE admin_configuration DROP data_fts');
    }
}
