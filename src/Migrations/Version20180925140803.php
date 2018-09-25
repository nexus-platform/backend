<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180925140803 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE app_settings (id INT AUTO_INCREMENT NOT NULL, mail_host VARCHAR(255) NOT NULL, mail_port INT NOT NULL, mail_username VARCHAR(255) NOT NULL, mail_password VARCHAR(255) NOT NULL, mail_encryption VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assessment_center (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, availability_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assessment_center_service (id INT AUTO_INCREMENT NOT NULL, ac_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, price DOUBLE PRECISION DEFAULT \'0\' NOT NULL, currency VARCHAR(255) DEFAULT \'GBP\' NOT NULL, description VARCHAR(255) DEFAULT NULL, attendants_number INT DEFAULT 1 NOT NULL, INDEX IDX_3BE592D3D2E3ED2F (ac_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assessment_center_service_assessor (id INT AUTO_INCREMENT NOT NULL, ac_service_id INT DEFAULT NULL, assessor_id INT DEFAULT NULL, INDEX IDX_5A1E7965664D13D3 (ac_service_id), INDEX IDX_5A1E7965A5E4B630 (assessor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assessment_center_user (id INT AUTO_INCREMENT NOT NULL, ac_id INT DEFAULT NULL, user_id INT DEFAULT NULL, is_admin INT NOT NULL, INDEX IDX_764B184DD2E3ED2F (ac_id), INDEX IDX_764B184DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assessment_form (id INT AUTO_INCREMENT NOT NULL, center_id INT DEFAULT NULL, student_id INT DEFAULT NULL, status TINYINT(1) NOT NULL, published_at DATETIME NOT NULL, name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, sex VARCHAR(255) DEFAULT NULL, date_of_birth DATETIME DEFAULT NULL, home_address VARCHAR(255) DEFAULT NULL, term_address VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, previously_assessed VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, dsa_assessed_email VARCHAR(255) DEFAULT NULL, dsa_assessed_attachement VARCHAR(255) DEFAULT NULL, student_finance_england VARCHAR(255) DEFAULT NULL, sfw VARCHAR(255) DEFAULT NULL, sfni VARCHAR(255) DEFAULT NULL, saas VARCHAR(255) DEFAULT NULL, nhs VARCHAR(255) DEFAULT NULL, dsa_eligibility_letter VARCHAR(255) DEFAULT NULL, customer_reference_number VARCHAR(255) DEFAULT NULL, course_title VARCHAR(255) DEFAULT NULL, course_type VARCHAR(255) DEFAULT NULL, select_type VARCHAR(255) DEFAULT NULL, year_of_study VARCHAR(255) DEFAULT NULL, course_date_start DATETIME DEFAULT NULL, course_date_end DATETIME DEFAULT NULL, learning_name VARCHAR(255) DEFAULT NULL, learning_address VARCHAR(255) DEFAULT NULL, disability_team_contact VARCHAR(255) DEFAULT NULL, disability_team_tel VARCHAR(255) DEFAULT NULL, disability_team_email VARCHAR(255) DEFAULT NULL, course_leader_contact VARCHAR(255) DEFAULT NULL, course_leader_tel VARCHAR(255) DEFAULT NULL, course_leader_email VARCHAR(255) DEFAULT NULL, permission_share VARCHAR(512) DEFAULT NULL, permission VARCHAR(512) DEFAULT NULL, type_disability VARCHAR(255) DEFAULT NULL, main_difficulties VARCHAR(255) DEFAULT NULL, type_of_support VARCHAR(255) DEFAULT NULL, type_of_equipment VARCHAR(255) DEFAULT NULL, special_access_requirements VARCHAR(255) DEFAULT NULL, dsa_eligibility_letter_current VARCHAR(255) DEFAULT NULL, diagnostic_assessment_documents VARCHAR(255) DEFAULT NULL, assurance_and_training VARCHAR(255) DEFAULT NULL, given_name VARCHAR(255) DEFAULT NULL, last_name_req VARCHAR(255) DEFAULT NULL, INDEX IDX_C0FBC0435932F377 (center_id), UNIQUE INDEX UNIQ_C0FBC043CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, alpha_two_code VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disability_officer (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_46EF298CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE officer_form (disability_officer_id INT NOT NULL, dsa_form_id INT NOT NULL, INDEX IDX_BDC4383CFA9D4790 (disability_officer_id), INDEX IDX_BDC4383C93705A43 (dsa_form_id), PRIMARY KEY(disability_officer_id, dsa_form_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dsa_form (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, base VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, content JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dsa_form_filled (id INT AUTO_INCREMENT NOT NULL, student_id INT DEFAULT NULL, form_id INT DEFAULT NULL, content JSON NOT NULL, signatures JSON NOT NULL, comments JSON NOT NULL, created_at INT NOT NULL, filename VARCHAR(255) DEFAULT NULL, status INT DEFAULT 0 NOT NULL, INDEX IDX_E4590E99CB944F1A (student_id), INDEX IDX_E4590E995FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dsa_slim (id INT AUTO_INCREMENT NOT NULL, center_id INT DEFAULT NULL, student_id INT DEFAULT NULL, customer_reference_number VARCHAR(255) NOT NULL, forename VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, sex VARCHAR(255) NOT NULL, dob_day VARCHAR(255) NOT NULL, dob_month VARCHAR(255) NOT NULL, dob_year VARCHAR(255) NOT NULL, excluding VARCHAR(255) NOT NULL, saas VARCHAR(255) NOT NULL, healthcare VARCHAR(255) NOT NULL, receipt VARCHAR(255) NOT NULL, motability_car VARCHAR(255) NOT NULL, disabilitydetails VARCHAR(255) NOT NULL, disabilitydetailsfile VARCHAR(255) NOT NULL, long_termadverse_effect VARCHAR(255) NOT NULL, learning_difficulty VARCHAR(255) NOT NULL, autistic_spectrum_disorders VARCHAR(255) NOT NULL, la_day VARCHAR(255) NOT NULL, la_month VARCHAR(255) NOT NULL, la_year VARCHAR(255) NOT NULL, pc VARCHAR(255) NOT NULL, working_order VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, age VARCHAR(255) NOT NULL, processor VARCHAR(255) NOT NULL, agree1 VARCHAR(255) NOT NULL, agree2 VARCHAR(255) NOT NULL, agree3 VARCHAR(255) NOT NULL, sortcode VARCHAR(255) NOT NULL, accountnumber VARCHAR(255) NOT NULL, building VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, today_day VARCHAR(255) NOT NULL, today_month VARCHAR(255) NOT NULL, today_year VARCHAR(255) NOT NULL, signed VARCHAR(255) NOT NULL, INDEX IDX_2A93CF465932F377 (center_id), UNIQUE INDEX UNIQ_2A93CF46CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ea_appointment (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, student_id INT DEFAULT NULL, service_id INT DEFAULT NULL, book_datetime DATETIME NOT NULL, start_datetime DATETIME NOT NULL, end_datetime DATETIME NOT NULL, notes VARCHAR(255) DEFAULT NULL, hash VARCHAR(255) NOT NULL, is_unavailable INT NOT NULL, google_calendar_id VARCHAR(255) DEFAULT NULL, INDEX IDX_D1F0178DA53A8AA (provider_id), INDEX IDX_D1F0178DCB944F1A (student_id), INDEX idx_appointment_service (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, uid VARCHAR(255) NOT NULL, original VARCHAR(255) NOT NULL, extension VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, center_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT \'Name\' NOT NULL, show_name TINYINT(1) DEFAULT \'1\' NOT NULL, last_name VARCHAR(255) NOT NULL, show_last_name TINYINT(1) NOT NULL, sex VARCHAR(255) NOT NULL, show_sex TINYINT(1) NOT NULL, date_of_birth VARCHAR(255) NOT NULL, show_date_of_birth TINYINT(1) NOT NULL, home_address VARCHAR(255) NOT NULL, show_home_address TINYINT(1) NOT NULL, term_address VARCHAR(255) NOT NULL, show_term_address TINYINT(1) NOT NULL, phone VARCHAR(255) NOT NULL, show_phone TINYINT(1) NOT NULL, mobile VARCHAR(255) NOT NULL, show_mobile TINYINT(1) NOT NULL, email VARCHAR(255) NOT NULL, show_email TINYINT(1) NOT NULL, previously_assessed VARCHAR(255) NOT NULL, show_previously_assessed TINYINT(1) NOT NULL, date VARCHAR(255) NOT NULL, show_date TINYINT(1) NOT NULL, dsa_assessed_email VARCHAR(255) NOT NULL, show_dsa_assessed_email TINYINT(1) NOT NULL, dsa_assessed_attachement VARCHAR(255) NOT NULL, show_dsa_assessed_attachement TINYINT(1) NOT NULL, student_finance_england VARCHAR(255) NOT NULL, show_student_finance_england TINYINT(1) NOT NULL, sfw VARCHAR(255) NOT NULL, show_sfw TINYINT(1) NOT NULL, sfni VARCHAR(255) NOT NULL, show_sfni TINYINT(1) NOT NULL, saas VARCHAR(255) NOT NULL, show_saas TINYINT(1) NOT NULL, nhs VARCHAR(255) NOT NULL, show_nhs TINYINT(1) NOT NULL, dsa_eligibility_letter VARCHAR(255) NOT NULL, show_dsa_eligibility_letter TINYINT(1) NOT NULL, customer_reference_number VARCHAR(255) NOT NULL, show_customer_reference_number TINYINT(1) NOT NULL, course_title VARCHAR(255) NOT NULL, show_course_title TINYINT(1) NOT NULL, course_type VARCHAR(255) NOT NULL, show_course_type TINYINT(1) NOT NULL, select_type VARCHAR(255) NOT NULL, show_select_type TINYINT(1) NOT NULL, year_of_study VARCHAR(255) NOT NULL, show_year_of_study TINYINT(1) NOT NULL, course_dates VARCHAR(255) NOT NULL, show_course_dates TINYINT(1) NOT NULL, learning_name VARCHAR(255) NOT NULL, show_learning_name TINYINT(1) NOT NULL, learning_address VARCHAR(255) NOT NULL, show_learning_address TINYINT(1) NOT NULL, disability_team_contact VARCHAR(255) NOT NULL, show_disability_team_contact TINYINT(1) NOT NULL, disability_team_tel VARCHAR(255) NOT NULL, show_disability_team_tel TINYINT(1) NOT NULL, disability_team_email VARCHAR(255) NOT NULL, show_disability_team_email TINYINT(1) NOT NULL, course_leader_contact VARCHAR(255) NOT NULL, show_course_leader_contact TINYINT(1) NOT NULL, course_leader_tel VARCHAR(255) NOT NULL, show_course_leader_tel TINYINT(1) NOT NULL, course_leader_email VARCHAR(255) NOT NULL, show_course_leader_email TINYINT(1) NOT NULL, permission_share VARCHAR(512) NOT NULL, show_permission_share TINYINT(1) NOT NULL, permission VARCHAR(512) NOT NULL, show_permission TINYINT(1) NOT NULL, type_disability VARCHAR(255) NOT NULL, show_type_disability TINYINT(1) NOT NULL, main_difficulties VARCHAR(255) NOT NULL, show_main_difficulties TINYINT(1) NOT NULL, type_of_support VARCHAR(255) NOT NULL, show_type_of_support TINYINT(1) NOT NULL, type_of_equipment VARCHAR(255) NOT NULL, show_type_of_equipment TINYINT(1) NOT NULL, special_access_requirements VARCHAR(255) NOT NULL, show_special_access_requirements TINYINT(1) NOT NULL, dsa_eligibility_letter_current VARCHAR(255) NOT NULL, show_dsa_eligibility_letter_current TINYINT(1) NOT NULL, diagnostic_assessment_documents VARCHAR(255) NOT NULL, show_diagnostic_assessment_documents TINYINT(1) NOT NULL, assurance_and_training VARCHAR(255) NOT NULL, show_assurance_and_training TINYINT(1) NOT NULL, given_name VARCHAR(255) NOT NULL, show_given_name TINYINT(1) NOT NULL, last_name_req VARCHAR(255) NOT NULL, show_last_name_req TINYINT(1) NOT NULL, INDEX IDX_EA750E85932F377 (center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nmh (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, company_registered_since VARCHAR(255) NOT NULL, company_reg_number VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, bands VARCHAR(255) NOT NULL, distance_learner TINYINT(1) NOT NULL, standard_business_hours VARCHAR(255) NOT NULL, evening_appointments TINYINT(1) NOT NULL, weekend_appointments TINYINT(1) NOT NULL, regions_supplied LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', institutions_survised LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2E32C3B8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) NOT NULL, headline VARCHAR(255) NOT NULL, created_at INT NOT NULL, status INT DEFAULT 1 NOT NULL, type INT DEFAULT 1 NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX idx_notif_type (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preregister (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, expires DATETIME NOT NULL, hash VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qr_code (id INT AUTO_INCREMENT NOT NULL, student_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, random_code VARCHAR(255) NOT NULL, created_at INT NOT NULL, INDEX IDX_7D8B1FB5CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE university (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, manager_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, state_province VARCHAR(255) DEFAULT NULL, domains LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', pages LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', UNIQUE INDEX UNIQ_A07A85EC5F37A13B (token), INDEX IDX_A07A85ECF92F3E70 (country_id), INDEX IDX_A07A85EC783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE university_dsa_form (id INT AUTO_INCREMENT NOT NULL, univ_id INT DEFAULT NULL, dsa_form_id INT DEFAULT NULL, dsa_form_slug VARCHAR(255) NOT NULL, active INT NOT NULL, INDEX IDX_5E393F8252B4B886 (univ_id), INDEX IDX_5E393F8293705A43 (dsa_form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, university_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, pre_register JSON DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, status INT DEFAULT 1 NOT NULL, token VARCHAR(255) NOT NULL, created_at INT NOT NULL, address VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, signature LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6495F37A13B (token), INDEX IDX_8D93D649309D1878 (university_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_invitation (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, ac_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, text VARCHAR(255) DEFAULT NULL, token VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_567AA74EF624B39D (sender_id), INDEX IDX_567AA74ED2E3ED2F (ac_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assessment_center_service ADD CONSTRAINT FK_3BE592D3D2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965664D13D3 FOREIGN KEY (ac_service_id) REFERENCES assessment_center_service (id)');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965A5E4B630 FOREIGN KEY (assessor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DD2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assessment_form ADD CONSTRAINT FK_C0FBC0435932F377 FOREIGN KEY (center_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE assessment_form ADD CONSTRAINT FK_C0FBC043CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE disability_officer ADD CONSTRAINT FK_46EF298CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE officer_form ADD CONSTRAINT FK_BDC4383CFA9D4790 FOREIGN KEY (disability_officer_id) REFERENCES disability_officer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE officer_form ADD CONSTRAINT FK_BDC4383C93705A43 FOREIGN KEY (dsa_form_id) REFERENCES dsa_form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dsa_form_filled ADD CONSTRAINT FK_E4590E99CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dsa_form_filled ADD CONSTRAINT FK_E4590E995FF69B7D FOREIGN KEY (form_id) REFERENCES dsa_form (id)');
        $this->addSql('ALTER TABLE dsa_slim ADD CONSTRAINT FK_2A93CF465932F377 FOREIGN KEY (center_id) REFERENCES disability_officer (id)');
        $this->addSql('ALTER TABLE dsa_slim ADD CONSTRAINT FK_2A93CF46CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DA53A8AA FOREIGN KEY (provider_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DCB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DED5CA9E6 FOREIGN KEY (service_id) REFERENCES assessment_center_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE label ADD CONSTRAINT FK_EA750E85932F377 FOREIGN KEY (center_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE nmh ADD CONSTRAINT FK_2E32C3B8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB5CB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE university ADD CONSTRAINT FK_A07A85ECF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE university ADD CONSTRAINT FK_A07A85EC783E3463 FOREIGN KEY (manager_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8252B4B886 FOREIGN KEY (univ_id) REFERENCES university (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8293705A43 FOREIGN KEY (dsa_form_id) REFERENCES dsa_form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649309D1878 FOREIGN KEY (university_id) REFERENCES university (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_invitation ADD CONSTRAINT FK_567AA74EF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_invitation ADD CONSTRAINT FK_567AA74ED2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE assessment_center_service DROP FOREIGN KEY FK_3BE592D3D2E3ED2F');
        $this->addSql('ALTER TABLE assessment_center_user DROP FOREIGN KEY FK_764B184DD2E3ED2F');
        $this->addSql('ALTER TABLE assessment_form DROP FOREIGN KEY FK_C0FBC0435932F377');
        $this->addSql('ALTER TABLE label DROP FOREIGN KEY FK_EA750E85932F377');
        $this->addSql('ALTER TABLE user_invitation DROP FOREIGN KEY FK_567AA74ED2E3ED2F');
        $this->addSql('ALTER TABLE assessment_center_service_assessor DROP FOREIGN KEY FK_5A1E7965664D13D3');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DED5CA9E6');
        $this->addSql('ALTER TABLE university DROP FOREIGN KEY FK_A07A85ECF92F3E70');
        $this->addSql('ALTER TABLE officer_form DROP FOREIGN KEY FK_BDC4383CFA9D4790');
        $this->addSql('ALTER TABLE dsa_slim DROP FOREIGN KEY FK_2A93CF465932F377');
        $this->addSql('ALTER TABLE officer_form DROP FOREIGN KEY FK_BDC4383C93705A43');
        $this->addSql('ALTER TABLE dsa_form_filled DROP FOREIGN KEY FK_E4590E995FF69B7D');
        $this->addSql('ALTER TABLE university_dsa_form DROP FOREIGN KEY FK_5E393F8293705A43');
        $this->addSql('ALTER TABLE university_dsa_form DROP FOREIGN KEY FK_5E393F8252B4B886');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649309D1878');
        $this->addSql('ALTER TABLE assessment_center_service_assessor DROP FOREIGN KEY FK_5A1E7965A5E4B630');
        $this->addSql('ALTER TABLE assessment_center_user DROP FOREIGN KEY FK_764B184DA76ED395');
        $this->addSql('ALTER TABLE assessment_form DROP FOREIGN KEY FK_C0FBC043CB944F1A');
        $this->addSql('ALTER TABLE disability_officer DROP FOREIGN KEY FK_46EF298CA76ED395');
        $this->addSql('ALTER TABLE dsa_form_filled DROP FOREIGN KEY FK_E4590E99CB944F1A');
        $this->addSql('ALTER TABLE dsa_slim DROP FOREIGN KEY FK_2A93CF46CB944F1A');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DA53A8AA');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DCB944F1A');
        $this->addSql('ALTER TABLE nmh DROP FOREIGN KEY FK_2E32C3B8A76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB5CB944F1A');
        $this->addSql('ALTER TABLE university DROP FOREIGN KEY FK_A07A85EC783E3463');
        $this->addSql('ALTER TABLE user_invitation DROP FOREIGN KEY FK_567AA74EF624B39D');
        $this->addSql('DROP TABLE app_settings');
        $this->addSql('DROP TABLE assessment_center');
        $this->addSql('DROP TABLE assessment_center_service');
        $this->addSql('DROP TABLE assessment_center_service_assessor');
        $this->addSql('DROP TABLE assessment_center_user');
        $this->addSql('DROP TABLE assessment_form');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE disability_officer');
        $this->addSql('DROP TABLE officer_form');
        $this->addSql('DROP TABLE dsa_form');
        $this->addSql('DROP TABLE dsa_form_filled');
        $this->addSql('DROP TABLE dsa_slim');
        $this->addSql('DROP TABLE ea_appointment');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE nmh');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE preregister');
        $this->addSql('DROP TABLE qr_code');
        $this->addSql('DROP TABLE university');
        $this->addSql('DROP TABLE university_dsa_form');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_invitation');
    }
}
