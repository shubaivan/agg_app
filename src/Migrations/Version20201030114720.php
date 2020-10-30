<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201030114720 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
        CREATE OR REPLACE FUNCTION remove_products_by_batch(text, int) RETURNS INTEGER AS $$
DECLARE result int := 1;
BEGIN
		WHILE result > 0
        LOOP

				DELETE FROM
					products
				WHERE
					id IN (
						SELECT
							id
						FROM
							products
						WHERE
							shop_relation_id =  (SELECT s.id FROM shop as s
										WHERE s.shop_name = $1)
						LIMIT $2
				);																	

				select count(*) into result
				from products
				where shop_relation_id = (SELECT s.id FROM shop as s
				WHERE s.shop_name = $1);


				raise notice \'result: % %\', result, E\'\n\';
        END LOOP;

		if result < 1 then return 1; 
		else return 2;
		end if;
END;
$$ LANGUAGE plpgsql VOLATILE;
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP FUNCTION IF EXISTS remove_products_by_batch');

    }
}
