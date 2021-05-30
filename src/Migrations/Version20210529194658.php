<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210529194658 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/embed","boolean","Intégrer dans une iframe","1");');
        $this->addSql('INSERT INTO config(`path`,`frontend_type`,`label`,`value`) VALUES("app/parent_url","text","Parent website","http://campus.local/base-de-donnees-plantes/");');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DELETE FROM config WHERE path = "app/embed";');
        $this->addSql('DELETE FROM config WHERE path = "app/parent_url";');
    }
}
