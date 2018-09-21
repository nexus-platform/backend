<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193155 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE assessment_center_service DROP FOREIGN KEY FK_3BE592D3D2E3ED2F');
        $this->addSql('ALTER TABLE assessment_center_service ADD CONSTRAINT FK_3BE592D3D2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DA53A8AA');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DCB944F1A');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DED5CA9E6');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DA53A8AA FOREIGN KEY (provider_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DCB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DED5CA9E6 FOREIGN KEY (service_id) REFERENCES assessment_center_service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB5CB944F1A');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB5CB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE university DROP FOREIGN KEY FK_A07A85EC783E3463');
        $this->addSql('ALTER TABLE university ADD CONSTRAINT FK_A07A85EC783E3463 FOREIGN KEY (manager_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE university_dsa_form DROP FOREIGN KEY FK_5E393F8252B4B886');
        $this->addSql('ALTER TABLE university_dsa_form DROP FOREIGN KEY FK_5E393F8293705A43');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8252B4B886 FOREIGN KEY (univ_id) REFERENCES university (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8293705A43 FOREIGN KEY (dsa_form_id) REFERENCES dsa_form (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649309D1878');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649309D1878 FOREIGN KEY (university_id) REFERENCES university (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_invitation DROP FOREIGN KEY FK_567AA74ED2E3ED2F');
        $this->addSql('ALTER TABLE user_invitation DROP FOREIGN KEY FK_567AA74EF624B39D');
        $this->addSql('ALTER TABLE user_invitation ADD CONSTRAINT FK_567AA74ED2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_invitation ADD CONSTRAINT FK_567AA74EF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE assessment_center_service DROP FOREIGN KEY FK_3BE592D3D2E3ED2F');
        $this->addSql('ALTER TABLE assessment_center_service ADD CONSTRAINT FK_3BE592D3D2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DA53A8AA');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DCB944F1A');
        $this->addSql('ALTER TABLE ea_appointment DROP FOREIGN KEY FK_D1F0178DED5CA9E6');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DA53A8AA FOREIGN KEY (provider_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DCB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DED5CA9E6 FOREIGN KEY (service_id) REFERENCES assessment_center_service (id)');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB5CB944F1A');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB5CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE university DROP FOREIGN KEY FK_A07A85EC783E3463');
        $this->addSql('ALTER TABLE university ADD CONSTRAINT FK_A07A85EC783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE university_dsa_form DROP FOREIGN KEY FK_5E393F8252B4B886');
        $this->addSql('ALTER TABLE university_dsa_form DROP FOREIGN KEY FK_5E393F8293705A43');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8252B4B886 FOREIGN KEY (univ_id) REFERENCES university (id)');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8293705A43 FOREIGN KEY (dsa_form_id) REFERENCES dsa_form (id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649309D1878');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649309D1878 FOREIGN KEY (university_id) REFERENCES university (id)');
        $this->addSql('ALTER TABLE user_invitation DROP FOREIGN KEY FK_567AA74EF624B39D');
        $this->addSql('ALTER TABLE user_invitation DROP FOREIGN KEY FK_567AA74ED2E3ED2F');
        $this->addSql('ALTER TABLE user_invitation ADD CONSTRAINT FK_567AA74EF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_invitation ADD CONSTRAINT FK_567AA74ED2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id)');
    }
}
