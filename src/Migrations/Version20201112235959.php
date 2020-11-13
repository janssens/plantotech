<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201112235959 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/main_email","email","email principal","hello@example.com");');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/website_name","text","nom du site","plantotech");');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/register_code","text","utiliser un code d\'inscription","apis_mellifera");');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE TABLE config');
    }
}
