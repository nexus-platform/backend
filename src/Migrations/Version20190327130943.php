<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190327130943 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ea_entity_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE assessment_form');
        $this->addSql('DROP TABLE dsa_slim');
        $this->addSql('DROP TABLE ea_appointment');
        $this->addSql('DROP TABLE nmh');
        $this->addSql('ALTER TABLE app_settings CHANGE mail_encryption mail_encryption VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE assessment_center ADD ea_entity_type_id INT DEFAULT NULL, ADD nmh_company_registered_since VARCHAR(255) DEFAULT NULL, ADD nmh_company_reg_number VARCHAR(255) DEFAULT NULL, ADD nmh_type VARCHAR(255) DEFAULT NULL, ADD nmh_bands VARCHAR(255) DEFAULT NULL, ADD nmh_distance_learner TINYINT(1) DEFAULT NULL, ADD nmh_standard_business_hours VARCHAR(255) DEFAULT NULL, ADD nmh_evening_appointments TINYINT(1) DEFAULT NULL, ADD nmh_weekend_appointments TINYINT(1) DEFAULT NULL, ADD nmh_regions_supplied LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD nmh_institutions_survised LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', CHANGE url url VARCHAR(255) DEFAULT NULL, CHANGE availability_type availability_type VARCHAR(255) DEFAULT NULL, CHANGE automatic_booking automatic_booking INT NOT NULL');
        $this->addSql('ALTER TABLE assessment_center ADD CONSTRAINT FK_B42D7A4A84A6B6F1 FOREIGN KEY (ea_entity_type_id) REFERENCES ea_entity_type (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B42D7A4A84A6B6F1 ON assessment_center (ea_entity_type_id)');
        $this->addSql('ALTER TABLE assessment_center_service CHANGE ac_id ac_id INT DEFAULT NULL, CHANGE currency currency VARCHAR(255) DEFAULT \'GBP\' NOT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE assessment_center_service_assessor DROP FOREIGN KEY FK_5A1E7965664D13D3');
        $this->addSql('ALTER TABLE assessment_center_service_assessor DROP FOREIGN KEY FK_5A1E7965A5E4B630');
        $this->addSql('ALTER TABLE assessment_center_service_assessor CHANGE ac_service_id ac_service_id INT DEFAULT NULL, CHANGE assessor_id assessor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965664D13D3 FOREIGN KEY (ac_service_id) REFERENCES assessment_center_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965A5E4B630 FOREIGN KEY (assessor_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE assessment_center_user CHANGE ac_id ac_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE disability_officer CHANGE user_id user_id INT DEFAULT NULL, CHANGE url url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dsa_form CHANGE content content JSON NOT NULL');
        $this->addSql('ALTER TABLE dsa_form_filled DROP FOREIGN KEY FK_E4590E99CB944F1A');
        $this->addSql('ALTER TABLE dsa_form_filled CHANGE student_id student_id INT DEFAULT NULL, CHANGE form_id form_id INT DEFAULT NULL, CHANGE content content JSON NOT NULL, CHANGE signatures signatures JSON NOT NULL, CHANGE comments comments JSON NOT NULL, CHANGE filename filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dsa_form_filled ADD CONSTRAINT FK_E4590E99CB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE label CHANGE center_id center_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT \'Name\' NOT NULL');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qr_code CHANGE student_id student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE university CHANGE country_id country_id INT DEFAULT NULL, CHANGE manager_id manager_id INT DEFAULT NULL, CHANGE state_province state_province VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE university_dsa_form CHANGE univ_id univ_id INT DEFAULT NULL, CHANGE dsa_form_id dsa_form_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE university_id university_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE pre_register pre_register JSON DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE postcode postcode VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_invitation CHANGE sender_id sender_id INT DEFAULT NULL, CHANGE ac_id ac_id INT DEFAULT NULL, CHANGE text text VARCHAR(255) DEFAULT NULL');
        
        //EA Entity Type
        $this->addSql("INSERT INTO `ea_entity_type` (`id`, `name`) VALUES (1, 'AC')");
        $this->addSql("INSERT INTO `ea_entity_type` (`id`, `name`) VALUES (2, 'NMH')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ea_services DROP FOREIGN KEY FK_CFDA547F2F80CD73');
        $this->addSql('ALTER TABLE ea_appointments DROP FOREIGN KEY FK_88D6C08123E90DFC');
        $this->addSql('ALTER TABLE assessment_center DROP FOREIGN KEY FK_B42D7A4A84A6B6F1');
        $this->addSql('CREATE TABLE assessment_form (id INT AUTO_INCREMENT NOT NULL, center_id INT DEFAULT NULL, student_id INT DEFAULT NULL, status TINYINT(1) NOT NULL, published_at DATETIME NOT NULL, name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, last_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, sex VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, date_of_birth DATETIME DEFAULT \'NULL\', home_address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, term_address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, phone VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, mobile VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, previously_assessed VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, date DATETIME DEFAULT \'NULL\', dsa_assessed_email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, dsa_assessed_attachement VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, student_finance_england VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, sfw VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, sfni VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, saas VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, nhs VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, dsa_eligibility_letter VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, customer_reference_number VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, course_title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, course_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, select_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, year_of_study VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, course_date_start DATETIME DEFAULT \'NULL\', course_date_end DATETIME DEFAULT \'NULL\', learning_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, learning_address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, disability_team_contact VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, disability_team_tel VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, disability_team_email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, course_leader_contact VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, course_leader_tel VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, course_leader_email VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, permission_share VARCHAR(512) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, permission VARCHAR(512) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, type_disability VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, main_difficulties VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, type_of_support VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, type_of_equipment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, special_access_requirements VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, dsa_eligibility_letter_current VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, diagnostic_assessment_documents VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, assurance_and_training VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, given_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, last_name_req VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_C0FBC043CB944F1A (student_id), INDEX IDX_C0FBC0435932F377 (center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dsa_slim (id INT AUTO_INCREMENT NOT NULL, center_id INT DEFAULT NULL, student_id INT DEFAULT NULL, customer_reference_number VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, forename VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, surname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, sex VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, dob_day VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, dob_month VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, dob_year VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, excluding VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, saas VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, healthcare VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, receipt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, motability_car VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, disabilitydetails VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, disabilitydetailsfile VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, long_termadverse_effect VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, learning_difficulty VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, autistic_spectrum_disorders VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, la_day VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, la_month VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, la_year VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, pc VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, working_order VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, model VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, age VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, processor VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, agree1 VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, agree2 VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, agree3 VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, sortcode VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, accountnumber VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, building VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, fullname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, today_day VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, today_month VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, today_year VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, signed VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_2A93CF46CB944F1A (student_id), INDEX IDX_2A93CF465932F377 (center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ea_appointment (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, student_id INT DEFAULT NULL, service_id INT DEFAULT NULL, book_datetime DATETIME NOT NULL, start_datetime DATETIME NOT NULL, end_datetime DATETIME NOT NULL, notes VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, hash VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, is_unavailable INT NOT NULL, google_calendar_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, INDEX IDX_D1F0178DA53A8AA (provider_id), INDEX IDX_D1F0178DCB944F1A (student_id), INDEX idx_appointment_service (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nmh (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, contact_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, telephone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, company_registered_since VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, company_reg_number VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, bands VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, distance_learner TINYINT(1) NOT NULL, standard_business_hours VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, evening_appointments TINYINT(1) NOT NULL, weekend_appointments TINYINT(1) NOT NULL, regions_supplied LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\', institutions_survised LONGTEXT DEFAULT \'NULL\' COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\', url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_2E32C3B8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assessment_form ADD CONSTRAINT FK_C0FBC0435932F377 FOREIGN KEY (center_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE assessment_form ADD CONSTRAINT FK_C0FBC043CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dsa_slim ADD CONSTRAINT FK_2A93CF465932F377 FOREIGN KEY (center_id) REFERENCES disability_officer (id)');
        $this->addSql('ALTER TABLE dsa_slim ADD CONSTRAINT FK_2A93CF46CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DA53A8AA FOREIGN KEY (provider_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DCB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DED5CA9E6 FOREIGN KEY (service_id) REFERENCES assessment_center_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nmh ADD CONSTRAINT FK_2E32C3B8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE ea_entity_type');
        $this->addSql('ALTER TABLE app_settings CHANGE mail_encryption mail_encryption VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('DROP INDEX IDX_B42D7A4A84A6B6F1 ON assessment_center');
        $this->addSql('ALTER TABLE assessment_center DROP ea_entity_type_id, DROP nmh_company_registered_since, DROP nmh_company_reg_number, DROP nmh_type, DROP nmh_bands, DROP nmh_distance_learner, DROP nmh_standard_business_hours, DROP nmh_evening_appointments, DROP nmh_weekend_appointments, DROP nmh_regions_supplied, DROP nmh_institutions_survised, CHANGE automatic_booking automatic_booking INT DEFAULT NULL, CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE availability_type availability_type VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE assessment_center_service CHANGE ac_id ac_id INT DEFAULT NULL, CHANGE currency currency VARCHAR(255) DEFAULT \'\'GBP\'\' NOT NULL COLLATE utf8_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE assessment_center_service_assessor DROP FOREIGN KEY FK_5A1E7965664D13D3');
        $this->addSql('ALTER TABLE assessment_center_service_assessor DROP FOREIGN KEY FK_5A1E7965A5E4B630');
        $this->addSql('ALTER TABLE assessment_center_service_assessor CHANGE ac_service_id ac_service_id INT DEFAULT NULL, CHANGE assessor_id assessor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965664D13D3 FOREIGN KEY (ac_service_id) REFERENCES assessment_center_service (id)');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965A5E4B630 FOREIGN KEY (assessor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assessment_center_user CHANGE ac_id ac_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE disability_officer CHANGE user_id user_id INT DEFAULT NULL, CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE dsa_form CHANGE content content LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE dsa_form_filled DROP FOREIGN KEY FK_E4590E99CB944F1A');
        $this->addSql('ALTER TABLE dsa_form_filled CHANGE student_id student_id INT DEFAULT NULL, CHANGE form_id form_id INT DEFAULT NULL, CHANGE content content LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE signatures signatures LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE comments comments LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE filename filename VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE dsa_form_filled ADD CONSTRAINT FK_E4590E99CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE label CHANGE center_id center_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT \'\'Name\'\' NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE qr_code CHANGE student_id student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE university CHANGE country_id country_id INT DEFAULT NULL, CHANGE manager_id manager_id INT DEFAULT NULL, CHANGE state_province state_province VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE university_dsa_form CHANGE univ_id univ_id INT DEFAULT NULL, CHANGE dsa_form_id dsa_form_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE university_id university_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE lastname lastname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE pre_register pre_register LONGTEXT DEFAULT NULL COLLATE utf8mb4_bin, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE postcode postcode VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE user_invitation CHANGE sender_id sender_id INT DEFAULT NULL, CHANGE ac_id ac_id INT DEFAULT NULL, CHANGE text text VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
