<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310204624 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE association CHANGE comment comment VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_family CHANGE category_id category_id INT DEFAULT NULL, CHANGE parent_id parent_id INT DEFAULT NULL, CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_value CHANGE main_value_id main_value_id INT DEFAULT NULL, CHANGE value value VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE config CHANGE value value VARCHAR(255) DEFAULT NULL, CHANGE label label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD origin VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE plant CHANGE family_id family_id INT DEFAULT NULL, CHANGE name name VARCHAR(50) DEFAULT NULL, CHANGE rusticity rusticity INT DEFAULT NULL, CHANGE rusticity_comment rusticity_comment VARCHAR(100) DEFAULT NULL, CHANGE temperature temperature INT DEFAULT NULL, CHANGE woody woody SMALLINT DEFAULT NULL, CHANGE min_height min_height INT DEFAULT NULL, CHANGE max_height max_height INT DEFAULT NULL, CHANGE min_width min_width INT DEFAULT NULL, CHANGE max_width max_width INT DEFAULT NULL, CHANGE min_sexual_maturity min_sexual_maturity INT DEFAULT NULL, CHANGE max_sexual_maturity max_sexual_maturity INT DEFAULT NULL, CHANGE native_place native_place VARCHAR(200) DEFAULT NULL, CHANGE botany_leaf botany_leaf VARCHAR(500) DEFAULT NULL, CHANGE botany_branch botany_branch VARCHAR(500) DEFAULT NULL, CHANGE botany_root botany_root VARCHAR(500) DEFAULT NULL, CHANGE botany_flower botany_flower VARCHAR(500) DEFAULT NULL, CHANGE botany_fruit botany_fruit VARCHAR(500) DEFAULT NULL, CHANGE botany_seed botany_seed VARCHAR(500) DEFAULT NULL, CHANGE density density VARCHAR(20) DEFAULT NULL, CHANGE specificity specificity VARCHAR(100) DEFAULT NULL, CHANGE author author VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE property_or_attribute CHANGE filter_category_id filter_category_id INT DEFAULT NULL, CHANGE family_id family_id INT DEFAULT NULL, CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE rp_token rp_token VARCHAR(255) DEFAULT NULL, CHANGE rp_token_created_at rp_token_created_at DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE association CHANGE comment comment VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE attribute_family CHANGE category_id category_id INT DEFAULT NULL, CHANGE parent_id parent_id INT DEFAULT NULL, CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attribute_value CHANGE main_value_id main_value_id INT DEFAULT NULL, CHANGE value value VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE category CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE config CHANGE value value VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE label label VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE image DROP origin');
        $this->addSql('ALTER TABLE plant CHANGE family_id family_id INT DEFAULT NULL, CHANGE name name VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE rusticity rusticity INT DEFAULT NULL, CHANGE rusticity_comment rusticity_comment VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE temperature temperature INT DEFAULT NULL, CHANGE woody woody SMALLINT DEFAULT NULL, CHANGE min_height min_height INT DEFAULT NULL, CHANGE max_height max_height INT DEFAULT NULL, CHANGE min_width min_width INT DEFAULT NULL, CHANGE max_width max_width INT DEFAULT NULL, CHANGE min_sexual_maturity min_sexual_maturity INT DEFAULT NULL, CHANGE max_sexual_maturity max_sexual_maturity INT DEFAULT NULL, CHANGE native_place native_place VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_leaf botany_leaf VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_branch botany_branch VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_root botany_root VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_flower botany_flower VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_fruit botany_fruit VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE botany_seed botany_seed VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE density density VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE specificity specificity VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE author author VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE property_or_attribute CHANGE filter_category_id filter_category_id INT DEFAULT NULL, CHANGE family_id family_id INT DEFAULT NULL, CHANGE position position INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE rp_token rp_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE rp_token_created_at rp_token_created_at DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
