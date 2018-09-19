<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193153 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ea_appointment (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, student_id INT DEFAULT NULL, service_id INT DEFAULT NULL, book_datetime DATETIME NOT NULL, start_datetime DATETIME NOT NULL, end_datetime DATETIME NOT NULL, notes VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, is_unavailable INT NOT NULL, google_calendar_id VARCHAR(255) NOT NULL, INDEX IDX_D1F0178DA53A8AA (provider_id), INDEX IDX_D1F0178DCB944F1A (student_id), INDEX idx_appointment_service (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DA53A8AA FOREIGN KEY (provider_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DCB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ea_appointment ADD CONSTRAINT FK_D1F0178DED5CA9E6 FOREIGN KEY (service_id) REFERENCES assessment_center_service (id)');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ea_appointment');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }
}
