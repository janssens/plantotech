<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200901170314 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE plants_insolations CHANGE insolation_id insolation_id INT DEFAULT NULL, CHANGE ideal ideal TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE family_id family_id INT DEFAULT NULL, CHANGE name name VARCHAR(50) DEFAULT NULL, CHANGE life_cycle life_cycle SMALLINT DEFAULT NULL, CHANGE rusticity rusticity INT DEFAULT NULL, CHANGE rusticity_comment rusticity_comment VARCHAR(100) DEFAULT NULL, CHANGE temperature temperature INT DEFAULT NULL, CHANGE woody woody SMALLINT DEFAULT NULL, CHANGE min_height min_height INT DEFAULT NULL, CHANGE max_height max_height INT DEFAULT NULL, CHANGE root root SMALLINT DEFAULT NULL, CHANGE min_width min_width INT DEFAULT NULL, CHANGE max_width max_width INT DEFAULT NULL, CHANGE sucker sucker SMALLINT DEFAULT NULL, CHANGE limestone limestone SMALLINT DEFAULT NULL, CHANGE min_sexual_maturity min_sexual_maturity INT DEFAULT NULL, CHANGE max_sexual_maturity max_sexual_maturity INT DEFAULT NULL, CHANGE native_place native_place VARCHAR(200) DEFAULT NULL, CHANGE botany_leaf botany_leaf VARCHAR(500) DEFAULT NULL, CHANGE botany_branch botany_branch VARCHAR(500) DEFAULT NULL, CHANGE botany_root botany_root VARCHAR(500) DEFAULT NULL, CHANGE botany_flower botany_flower VARCHAR(500) DEFAULT NULL, CHANGE botany_fruit botany_fruit VARCHAR(500) DEFAULT NULL, CHANGE botany_seed botany_seed VARCHAR(500) DEFAULT NULL, CHANGE density density VARCHAR(20) DEFAULT NULL, CHANGE leaf_density leaf_density SMALLINT DEFAULT NULL, CHANGE foliage foliage SMALLINT DEFAULT NULL, CHANGE specificity specificity VARCHAR(100) DEFAULT NULL, CHANGE priority priority SMALLINT DEFAULT NULL, CHANGE drought_tolerance drought_tolerance INT DEFAULT NULL, CHANGE diseases_and_pest diseases_and_pest VARCHAR(100) DEFAULT NULL, CHANGE author author VARCHAR(100) DEFAULT NULL, CHANGE stratum stratum INT DEFAULT NULL');
        $this->addSql('CREATE FULLTEXT INDEX IDX_AB030D72A2A718195E237E06 ON plant (latin_name, name)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE attribute CHANGE family_id family_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_family CHANGE category_id category_id INT DEFAULT NULL, CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE association CHANGE comment comment VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nutrient CHANGE name name VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE association CHANGE comment comment VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE attribute CHANGE family_id family_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_family CHANGE category_id category_id INT DEFAULT NULL, CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nutrient CHANGE name name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX IDX_AB030D72A2A718195E237E06 ON plant');
        $this->addSql('ALTER TABLE plant CHANGE family_id family_id INT DEFAULT NULL, CHANGE name name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE life_cycle life_cycle SMALLINT DEFAULT NULL, CHANGE rusticity rusticity INT DEFAULT NULL, CHANGE rusticity_comment rusticity_comment VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE temperature temperature INT DEFAULT NULL, CHANGE woody woody SMALLINT DEFAULT NULL, CHANGE min_height min_height INT DEFAULT NULL, CHANGE max_height max_height INT DEFAULT NULL, CHANGE root root SMALLINT DEFAULT NULL, CHANGE min_width min_width INT DEFAULT NULL, CHANGE max_width max_width INT DEFAULT NULL, CHANGE sucker sucker SMALLINT DEFAULT NULL, CHANGE limestone limestone SMALLINT DEFAULT NULL, CHANGE min_sexual_maturity min_sexual_maturity INT DEFAULT NULL, CHANGE max_sexual_maturity max_sexual_maturity INT DEFAULT NULL, CHANGE native_place native_place VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_leaf botany_leaf VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_branch botany_branch VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_root botany_root VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_flower botany_flower VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_fruit botany_fruit VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_seed botany_seed VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE density density VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE leaf_density leaf_density SMALLINT DEFAULT NULL, CHANGE foliage foliage SMALLINT DEFAULT NULL, CHANGE specificity specificity VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE priority priority SMALLINT DEFAULT NULL, CHANGE drought_tolerance drought_tolerance INT DEFAULT NULL, CHANGE diseases_and_pest diseases_and_pest VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE author author VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE stratum stratum INT DEFAULT NULL');
        $this->addSql('ALTER TABLE plants_insolations CHANGE insolation_id insolation_id INT DEFAULT NULL, CHANGE ideal ideal TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
