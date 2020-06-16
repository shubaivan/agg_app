<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200616072939 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TEXT SEARCH CONFIGURATION my_swedish (
               COPY = swedish
            )
        ');
        $this->addSql('ALTER TEXT SEARCH CONFIGURATION my_swedish
   DROP MAPPING FOR hword_asciipart');
        $this->addSql('ALTER TEXT SEARCH CONFIGURATION my_swedish
   DROP MAPPING FOR hword_part');
    }

    public function down(Schema $schema) : void
    {
    }
}
