<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201117212755 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, plant1_id INT NOT NULL, plant2_id INT NOT NULL, type SMALLINT NOT NULL, comment VARCHAR(100) DEFAULT NULL, INDEX IDX_FD8521CC53148E1D (plant1_id), INDEX IDX_FD8521CC41A121F3 (plant2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute (id INT AUTO_INCREMENT NOT NULL, family_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, type SMALLINT NOT NULL, code VARCHAR(50) NOT NULL, position INT DEFAULT NULL, INDEX IDX_FA7AEFFBC35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute_family (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT NULL, INDEX IDX_87070F0112469DE2 (category_id), INDEX IDX_87070F01727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute_value (id INT AUTO_INCREMENT NOT NULL, attribute_id INT NOT NULL, value VARCHAR(255) DEFAULT NULL, code VARCHAR(50) NOT NULL, INDEX IDX_FE4FBB82B6E62EFA (attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute_value_plant (attribute_value_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_69797B8965A22152 (attribute_value_id), INDEX IDX_69797B891D935652 (plant_id), PRIMARY KEY(attribute_value_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, frontend_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D48A2F7CB548B0F (path), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C53D045F1D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE insolation (id INT AUTO_INCREMENT NOT NULL, type SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_value (id INT AUTO_INCREMENT NOT NULL, attribute_value_id INT NOT NULL, attribute_id INT NOT NULL, label VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_BE2BEB7265A22152 (attribute_value_id), INDEX IDX_BE2BEB72B6E62EFA (attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE main_value_plant (main_value_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_802640CF2002AAD7 (main_value_id), INDEX IDX_802640CF1D935652 (plant_id), PRIMARY KEY(main_value_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plant (id INT AUTO_INCREMENT NOT NULL, family_id INT DEFAULT NULL, latin_name VARCHAR(50) NOT NULL, name VARCHAR(50) DEFAULT NULL, life_cycle SMALLINT DEFAULT NULL, rusticity INT DEFAULT NULL, rusticity_comment VARCHAR(100) DEFAULT NULL, temperature INT DEFAULT NULL, woody SMALLINT DEFAULT NULL, min_height INT DEFAULT NULL, max_height INT DEFAULT NULL, root SMALLINT DEFAULT NULL, min_width INT DEFAULT NULL, max_width INT DEFAULT NULL, sucker SMALLINT DEFAULT NULL, limestone SMALLINT DEFAULT NULL, min_sexual_maturity INT DEFAULT NULL, max_sexual_maturity INT DEFAULT NULL, native_place VARCHAR(200) DEFAULT NULL, botany_leaf VARCHAR(500) DEFAULT NULL, botany_branch VARCHAR(500) DEFAULT NULL, botany_root VARCHAR(500) DEFAULT NULL, botany_flower VARCHAR(500) DEFAULT NULL, botany_fruit VARCHAR(500) DEFAULT NULL, botany_seed VARCHAR(500) DEFAULT NULL, density VARCHAR(20) DEFAULT NULL, leaf_density SMALLINT DEFAULT NULL, foliage SMALLINT DEFAULT NULL, interest LONGTEXT DEFAULT NULL, specificity VARCHAR(100) DEFAULT NULL, priority SMALLINT DEFAULT NULL, drought_tolerance INT DEFAULT NULL, diseases_and_pest VARCHAR(100) DEFAULT NULL, author VARCHAR(100) DEFAULT NULL, stratum INT DEFAULT NULL, INDEX IDX_AB030D72C35E566A (family_id), FULLTEXT INDEX IDX_AB030D72A2A718195E237E06 (latin_name, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plant_family (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plants_insolations (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, insolation_id INT DEFAULT NULL, ideal TINYINT(1) DEFAULT NULL, INDEX IDX_5A91F3901D935652 (plant_id), INDEX IDX_5A91F390C80C469C (insolation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plants_ports (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, type SMALLINT NOT NULL, INDEX IDX_E82ABE321D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plants_ports_port (plants_ports_id INT NOT NULL, port_id INT NOT NULL, INDEX IDX_9B85454AE053BEC6 (plants_ports_id), INDEX IDX_9B85454A76E92A9C (port_id), PRIMARY KEY(plants_ports_id, port_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE port (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE port_plants_ports (port_id INT NOT NULL, plants_ports_id INT NOT NULL, INDEX IDX_FAEC180976E92A9C (port_id), INDEX IDX_FAEC1809E053BEC6 (plants_ports_id), PRIMARY KEY(port_id, plants_ports_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, family_id INT DEFAULT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, position INT DEFAULT NULL, available_values JSON DEFAULT NULL, INDEX IDX_8BF21CDEC35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, name VARCHAR(300) NOT NULL, INDEX IDX_5F8A7F731D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, rp_token VARCHAR(255) DEFAULT NULL, rp_token_created_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D6493F275AB (rp_token), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC53148E1D FOREIGN KEY (plant1_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC41A121F3 FOREIGN KEY (plant2_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT FK_FA7AEFFBC35E566A FOREIGN KEY (family_id) REFERENCES attribute_family (id)');
        $this->addSql('ALTER TABLE attribute_family ADD CONSTRAINT FK_87070F0112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE attribute_family ADD CONSTRAINT FK_87070F01727ACA70 FOREIGN KEY (parent_id) REFERENCES attribute_family (id)');
        $this->addSql('ALTER TABLE attribute_value ADD CONSTRAINT FK_FE4FBB82B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id)');
        $this->addSql('ALTER TABLE attribute_value_plant ADD CONSTRAINT FK_69797B8965A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attribute_value_plant ADD CONSTRAINT FK_69797B891D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE main_value ADD CONSTRAINT FK_BE2BEB7265A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id)');
        $this->addSql('ALTER TABLE main_value ADD CONSTRAINT FK_BE2BEB72B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id)');
        $this->addSql('ALTER TABLE main_value_plant ADD CONSTRAINT FK_802640CF2002AAD7 FOREIGN KEY (main_value_id) REFERENCES main_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE main_value_plant ADD CONSTRAINT FK_802640CF1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plant ADD CONSTRAINT FK_AB030D72C35E566A FOREIGN KEY (family_id) REFERENCES plant_family (id)');
        $this->addSql('ALTER TABLE plants_insolations ADD CONSTRAINT FK_5A91F3901D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE plants_insolations ADD CONSTRAINT FK_5A91F390C80C469C FOREIGN KEY (insolation_id) REFERENCES insolation (id)');
        $this->addSql('ALTER TABLE plants_ports ADD CONSTRAINT FK_E82ABE321D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE plants_ports_port ADD CONSTRAINT FK_9B85454AE053BEC6 FOREIGN KEY (plants_ports_id) REFERENCES plants_ports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plants_ports_port ADD CONSTRAINT FK_9B85454A76E92A9C FOREIGN KEY (port_id) REFERENCES port (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE port_plants_ports ADD CONSTRAINT FK_FAEC180976E92A9C FOREIGN KEY (port_id) REFERENCES port (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE port_plants_ports ADD CONSTRAINT FK_FAEC1809E053BEC6 FOREIGN KEY (plants_ports_id) REFERENCES plants_ports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDEC35E566A FOREIGN KEY (family_id) REFERENCES attribute_family (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F731D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attribute_value DROP FOREIGN KEY FK_FE4FBB82B6E62EFA');
        $this->addSql('ALTER TABLE main_value DROP FOREIGN KEY FK_BE2BEB72B6E62EFA');
        $this->addSql('ALTER TABLE attribute DROP FOREIGN KEY FK_FA7AEFFBC35E566A');
        $this->addSql('ALTER TABLE attribute_family DROP FOREIGN KEY FK_87070F01727ACA70');
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDEC35E566A');
        $this->addSql('ALTER TABLE attribute_value_plant DROP FOREIGN KEY FK_69797B8965A22152');
        $this->addSql('ALTER TABLE main_value DROP FOREIGN KEY FK_BE2BEB7265A22152');
        $this->addSql('ALTER TABLE attribute_family DROP FOREIGN KEY FK_87070F0112469DE2');
        $this->addSql('ALTER TABLE plants_insolations DROP FOREIGN KEY FK_5A91F390C80C469C');
        $this->addSql('ALTER TABLE main_value_plant DROP FOREIGN KEY FK_802640CF2002AAD7');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC53148E1D');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC41A121F3');
        $this->addSql('ALTER TABLE attribute_value_plant DROP FOREIGN KEY FK_69797B891D935652');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F1D935652');
        $this->addSql('ALTER TABLE main_value_plant DROP FOREIGN KEY FK_802640CF1D935652');
        $this->addSql('ALTER TABLE plants_insolations DROP FOREIGN KEY FK_5A91F3901D935652');
        $this->addSql('ALTER TABLE plants_ports DROP FOREIGN KEY FK_E82ABE321D935652');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F731D935652');
        $this->addSql('ALTER TABLE plant DROP FOREIGN KEY FK_AB030D72C35E566A');
        $this->addSql('ALTER TABLE plants_ports_port DROP FOREIGN KEY FK_9B85454AE053BEC6');
        $this->addSql('ALTER TABLE port_plants_ports DROP FOREIGN KEY FK_FAEC1809E053BEC6');
        $this->addSql('ALTER TABLE plants_ports_port DROP FOREIGN KEY FK_9B85454A76E92A9C');
        $this->addSql('ALTER TABLE port_plants_ports DROP FOREIGN KEY FK_FAEC180976E92A9C');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE attribute_family');
        $this->addSql('DROP TABLE attribute_value');
        $this->addSql('DROP TABLE attribute_value_plant');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE config');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE insolation');
        $this->addSql('DROP TABLE main_value');
        $this->addSql('DROP TABLE main_value_plant');
        $this->addSql('DROP TABLE plant');
        $this->addSql('DROP TABLE plant_family');
        $this->addSql('DROP TABLE plants_insolations');
        $this->addSql('DROP TABLE plants_ports');
        $this->addSql('DROP TABLE plants_ports_port');
        $this->addSql('DROP TABLE port');
        $this->addSql('DROP TABLE port_plants_ports');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP TABLE user');
    }
}
