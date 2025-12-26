<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210321083735 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/return_link","text","lien de retour","https://campus.universite-alveoles.fr/");');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/return_label","text","label du lien de retour","retour sur le campus");');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM config WHERE path = "app/return_link";');
        $this->addSql('DELETE FROM config WHERE path = "app/return_label";');
    }
}
