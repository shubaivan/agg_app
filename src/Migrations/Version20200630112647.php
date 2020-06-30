<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200630112647 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TEXT SEARCH DICTIONARY thesaurus_my_swedish (
             TEMPLATE = thesaurus,
             DictFile = thesaurus_my_swedish,
             Dictionary = pg_catalog.swedish_stem)
        ');

        $this->addSql('
            ALTER TEXT SEARCH CONFIGURATION my_swedish
            ALTER MAPPING FOR asciihword, asciiword, hword, word
            WITH thesaurus_my_swedish, pg_catalog.swedish_stem
        ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
