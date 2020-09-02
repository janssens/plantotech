<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200902162629 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE humidity (id INT AUTO_INCREMENT NOT NULL, value INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE humidity_plant (humidity_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_BD5F3B3E201FD671 (humidity_id), INDEX IDX_BD5F3B3E1D935652 (plant_id), PRIMARY KEY(humidity_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plants_insolations (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, insolation_id INT DEFAULT NULL, ideal TINYINT(1) DEFAULT NULL, INDEX IDX_5A91F3901D935652 (plant_id), INDEX IDX_5A91F390C80C469C (insolation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE humus (id INT AUTO_INCREMENT NOT NULL, quantity VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE humus_plant (humus_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_9515FBB1BDAEFB80 (humus_id), INDEX IDX_9515FBB11D935652 (plant_id), PRIMARY KEY(humus_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soil (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE soil_plant (soil_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_6E65C5D4C59CE9E2 (soil_id), INDEX IDX_6E65C5D41D935652 (plant_id), PRIMARY KEY(soil_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clay (id INT AUTO_INCREMENT NOT NULL, scale SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clay_plant (clay_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_9645D0257361EBEE (clay_id), INDEX IDX_9645D0251D935652 (plant_id), PRIMARY KEY(clay_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plant (id INT AUTO_INCREMENT NOT NULL, family_id INT DEFAULT NULL, latin_name VARCHAR(50) NOT NULL, name VARCHAR(50) DEFAULT NULL, life_cycle SMALLINT DEFAULT NULL, rusticity INT DEFAULT NULL, rusticity_comment VARCHAR(100) DEFAULT NULL, temperature INT DEFAULT NULL, woody SMALLINT DEFAULT NULL, min_height INT DEFAULT NULL, max_height INT DEFAULT NULL, root SMALLINT DEFAULT NULL, min_width INT DEFAULT NULL, max_width INT DEFAULT NULL, sucker SMALLINT DEFAULT NULL, limestone SMALLINT DEFAULT NULL, min_sexual_maturity INT DEFAULT NULL, max_sexual_maturity INT DEFAULT NULL, native_place VARCHAR(200) DEFAULT NULL, botany_leaf VARCHAR(500) DEFAULT NULL, botany_branch VARCHAR(500) DEFAULT NULL, botany_root VARCHAR(500) DEFAULT NULL, botany_flower VARCHAR(500) DEFAULT NULL, botany_fruit VARCHAR(500) DEFAULT NULL, botany_seed VARCHAR(500) DEFAULT NULL, density VARCHAR(20) DEFAULT NULL, leaf_density SMALLINT DEFAULT NULL, foliage SMALLINT DEFAULT NULL, interest LONGTEXT DEFAULT NULL, specificity VARCHAR(100) DEFAULT NULL, priority SMALLINT DEFAULT NULL, drought_tolerance INT DEFAULT NULL, diseases_and_pest VARCHAR(100) DEFAULT NULL, author VARCHAR(100) DEFAULT NULL, stratum INT DEFAULT NULL, INDEX IDX_AB030D72C35E566A (family_id), FULLTEXT INDEX IDX_AB030D72A2A718195E237E06 (latin_name, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE insolation (id INT AUTO_INCREMENT NOT NULL, type SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plant_family (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE port (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE port_plants_ports (port_id INT NOT NULL, plants_ports_id INT NOT NULL, INDEX IDX_FAEC180976E92A9C (port_id), INDEX IDX_FAEC1809E053BEC6 (plants_ports_id), PRIMARY KEY(port_id, plants_ports_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute (id INT AUTO_INCREMENT NOT NULL, family_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, type SMALLINT NOT NULL, INDEX IDX_FA7AEFFBC35E566A (family_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, name VARCHAR(300) NOT NULL, INDEX IDX_5F8A7F731D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute_value (id INT AUTO_INCREMENT NOT NULL, attribute_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_FE4FBB82B6E62EFA (attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute_value_plant (attribute_value_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_69797B8965A22152 (attribute_value_id), INDEX IDX_69797B891D935652 (plant_id), PRIMARY KEY(attribute_value_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flowering_and_crop (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, type SMALLINT NOT NULL, month SMALLINT NOT NULL, INDEX IDX_9B9089021D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ph (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ph_plant (ph_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_E1D20C365862D41C (ph_id), INDEX IDX_E1D20C361D935652 (plant_id), PRIMARY KEY(ph_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attribute_family (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT NULL, INDEX IDX_87070F0112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE association (id INT AUTO_INCREMENT NOT NULL, plant1_id INT NOT NULL, plant2_id INT NOT NULL, type SMALLINT NOT NULL, comment VARCHAR(100) DEFAULT NULL, INDEX IDX_FD8521CC53148E1D (plant1_id), INDEX IDX_FD8521CC41A121F3 (plant2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nutrient (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) DEFAULT NULL, symbol VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nutrient_plant (nutrient_id INT NOT NULL, plant_id INT NOT NULL, INDEX IDX_C69DD0ED27373320 (nutrient_id), INDEX IDX_C69DD0ED1D935652 (plant_id), PRIMARY KEY(nutrient_id, plant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C53D045F1D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plants_ports (id INT AUTO_INCREMENT NOT NULL, plant_id INT NOT NULL, type SMALLINT NOT NULL, INDEX IDX_E82ABE321D935652 (plant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plants_ports_port (plants_ports_id INT NOT NULL, port_id INT NOT NULL, INDEX IDX_9B85454AE053BEC6 (plants_ports_id), INDEX IDX_9B85454A76E92A9C (port_id), PRIMARY KEY(plants_ports_id, port_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE humidity_plant ADD CONSTRAINT FK_BD5F3B3E201FD671 FOREIGN KEY (humidity_id) REFERENCES humidity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE humidity_plant ADD CONSTRAINT FK_BD5F3B3E1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plants_insolations ADD CONSTRAINT FK_5A91F3901D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE plants_insolations ADD CONSTRAINT FK_5A91F390C80C469C FOREIGN KEY (insolation_id) REFERENCES insolation (id)');
        $this->addSql('ALTER TABLE humus_plant ADD CONSTRAINT FK_9515FBB1BDAEFB80 FOREIGN KEY (humus_id) REFERENCES humus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE humus_plant ADD CONSTRAINT FK_9515FBB11D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE soil_plant ADD CONSTRAINT FK_6E65C5D4C59CE9E2 FOREIGN KEY (soil_id) REFERENCES soil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE soil_plant ADD CONSTRAINT FK_6E65C5D41D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clay_plant ADD CONSTRAINT FK_9645D0257361EBEE FOREIGN KEY (clay_id) REFERENCES clay (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clay_plant ADD CONSTRAINT FK_9645D0251D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plant ADD CONSTRAINT FK_AB030D72C35E566A FOREIGN KEY (family_id) REFERENCES plant_family (id)');
        $this->addSql('ALTER TABLE port_plants_ports ADD CONSTRAINT FK_FAEC180976E92A9C FOREIGN KEY (port_id) REFERENCES port (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE port_plants_ports ADD CONSTRAINT FK_FAEC1809E053BEC6 FOREIGN KEY (plants_ports_id) REFERENCES plants_ports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attribute ADD CONSTRAINT FK_FA7AEFFBC35E566A FOREIGN KEY (family_id) REFERENCES attribute_family (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F731D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE attribute_value ADD CONSTRAINT FK_FE4FBB82B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id)');
        $this->addSql('ALTER TABLE attribute_value_plant ADD CONSTRAINT FK_69797B8965A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attribute_value_plant ADD CONSTRAINT FK_69797B891D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flowering_and_crop ADD CONSTRAINT FK_9B9089021D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE ph_plant ADD CONSTRAINT FK_E1D20C365862D41C FOREIGN KEY (ph_id) REFERENCES ph (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ph_plant ADD CONSTRAINT FK_E1D20C361D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attribute_family ADD CONSTRAINT FK_87070F0112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC53148E1D FOREIGN KEY (plant1_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE association ADD CONSTRAINT FK_FD8521CC41A121F3 FOREIGN KEY (plant2_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE nutrient_plant ADD CONSTRAINT FK_C69DD0ED27373320 FOREIGN KEY (nutrient_id) REFERENCES nutrient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nutrient_plant ADD CONSTRAINT FK_C69DD0ED1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F1D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE plants_ports ADD CONSTRAINT FK_E82ABE321D935652 FOREIGN KEY (plant_id) REFERENCES plant (id)');
        $this->addSql('ALTER TABLE plants_ports_port ADD CONSTRAINT FK_9B85454AE053BEC6 FOREIGN KEY (plants_ports_id) REFERENCES plants_ports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plants_ports_port ADD CONSTRAINT FK_9B85454A76E92A9C FOREIGN KEY (port_id) REFERENCES port (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE humidity_plant DROP FOREIGN KEY FK_BD5F3B3E201FD671');
        $this->addSql('ALTER TABLE humus_plant DROP FOREIGN KEY FK_9515FBB1BDAEFB80');
        $this->addSql('ALTER TABLE soil_plant DROP FOREIGN KEY FK_6E65C5D4C59CE9E2');
        $this->addSql('ALTER TABLE clay_plant DROP FOREIGN KEY FK_9645D0257361EBEE');
        $this->addSql('ALTER TABLE humidity_plant DROP FOREIGN KEY FK_BD5F3B3E1D935652');
        $this->addSql('ALTER TABLE plants_insolations DROP FOREIGN KEY FK_5A91F3901D935652');
        $this->addSql('ALTER TABLE humus_plant DROP FOREIGN KEY FK_9515FBB11D935652');
        $this->addSql('ALTER TABLE soil_plant DROP FOREIGN KEY FK_6E65C5D41D935652');
        $this->addSql('ALTER TABLE clay_plant DROP FOREIGN KEY FK_9645D0251D935652');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F731D935652');
        $this->addSql('ALTER TABLE attribute_value_plant DROP FOREIGN KEY FK_69797B891D935652');
        $this->addSql('ALTER TABLE flowering_and_crop DROP FOREIGN KEY FK_9B9089021D935652');
        $this->addSql('ALTER TABLE ph_plant DROP FOREIGN KEY FK_E1D20C361D935652');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC53148E1D');
        $this->addSql('ALTER TABLE association DROP FOREIGN KEY FK_FD8521CC41A121F3');
        $this->addSql('ALTER TABLE nutrient_plant DROP FOREIGN KEY FK_C69DD0ED1D935652');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F1D935652');
        $this->addSql('ALTER TABLE plants_ports DROP FOREIGN KEY FK_E82ABE321D935652');
        $this->addSql('ALTER TABLE plants_insolations DROP FOREIGN KEY FK_5A91F390C80C469C');
        $this->addSql('ALTER TABLE plant DROP FOREIGN KEY FK_AB030D72C35E566A');
        $this->addSql('ALTER TABLE port_plants_ports DROP FOREIGN KEY FK_FAEC180976E92A9C');
        $this->addSql('ALTER TABLE plants_ports_port DROP FOREIGN KEY FK_9B85454A76E92A9C');
        $this->addSql('ALTER TABLE attribute_value DROP FOREIGN KEY FK_FE4FBB82B6E62EFA');
        $this->addSql('ALTER TABLE attribute_value_plant DROP FOREIGN KEY FK_69797B8965A22152');
        $this->addSql('ALTER TABLE ph_plant DROP FOREIGN KEY FK_E1D20C365862D41C');
        $this->addSql('ALTER TABLE attribute DROP FOREIGN KEY FK_FA7AEFFBC35E566A');
        $this->addSql('ALTER TABLE attribute_family DROP FOREIGN KEY FK_87070F0112469DE2');
        $this->addSql('ALTER TABLE nutrient_plant DROP FOREIGN KEY FK_C69DD0ED27373320');
        $this->addSql('ALTER TABLE port_plants_ports DROP FOREIGN KEY FK_FAEC1809E053BEC6');
        $this->addSql('ALTER TABLE plants_ports_port DROP FOREIGN KEY FK_9B85454AE053BEC6');
        $this->addSql('DROP TABLE humidity');
        $this->addSql('DROP TABLE humidity_plant');
        $this->addSql('DROP TABLE plants_insolations');
        $this->addSql('DROP TABLE humus');
        $this->addSql('DROP TABLE humus_plant');
        $this->addSql('DROP TABLE soil');
        $this->addSql('DROP TABLE soil_plant');
        $this->addSql('DROP TABLE clay');
        $this->addSql('DROP TABLE clay_plant');
        $this->addSql('DROP TABLE plant');
        $this->addSql('DROP TABLE insolation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE plant_family');
        $this->addSql('DROP TABLE port');
        $this->addSql('DROP TABLE port_plants_ports');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP TABLE attribute_value');
        $this->addSql('DROP TABLE attribute_value_plant');
        $this->addSql('DROP TABLE flowering_and_crop');
        $this->addSql('DROP TABLE ph');
        $this->addSql('DROP TABLE ph_plant');
        $this->addSql('DROP TABLE attribute_family');
        $this->addSql('DROP TABLE association');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE nutrient');
        $this->addSql('DROP TABLE nutrient_plant');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE plants_ports');
        $this->addSql('DROP TABLE plants_ports_port');
    }
}
