<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200619120707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            DROP TRIGGER IF EXISTS group_created_at_time on products
        ');
        $this->addSql('
            CREATE OR REPLACE FUNCTION group_created_at_time() RETURNS TRIGGER
            AS $group_created_at_time$
                DECLARE
                    delta_time_key          timestamp;
                    delta_group_identity   varchar;
                    
                BEGIN
            
            
            
                    IF (TG_OP = \'UPDATE\') THEN
            
                        delta_time_key = NEW.created_at;
                        delta_group_identity = NEW.group_identity;
            
                    ELSIF (TG_OP = \'INSERT\') THEN
            
                        delta_time_key = NEW.created_at;
                        delta_group_identity = NEW.group_identity;
                                    
                    END IF;
                            
                            UPDATE products
                            SET 
                            created_at = delta_time_key
                            WHERE group_identity = delta_group_identity;    
            
                    RETURN NULL;
            
                END;
            $group_created_at_time$ LANGUAGE plpgsql;');
        $this->addSql('            
            CREATE TRIGGER group_created_at_time
            AFTER INSERT OR UPDATE ON products
                
            FOR EACH ROW 
            WHEN (pg_trigger_depth() = 0)
            EXECUTE PROCEDURE group_created_at_time();
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP FUNCTION IF EXISTS group_created_at_time');
        $this->addSql('
            DROP TRIGGER IF EXISTS group_created_at_time on products
        ');
    }
}
