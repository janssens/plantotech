<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313153827 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/oauth_text","text","Label du bouton de connexion wordpress","Se connecter avec le campus des Alveoles");');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/oauth_client_id","text","wordpress oauth client id","");');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/oauth_client_secret","password","wordpress oauth client secret","");');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM config WHERE path = "app/oauth_text";');
        $this->addSql('DELETE FROM config WHERE path = "app/oauth_client_id";');
        $this->addSql('DELETE FROM config WHERE path = "app/oauth_client_secret";');
    }
}
