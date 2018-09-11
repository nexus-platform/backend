<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193151 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE assessment_center_service (id INT AUTO_INCREMENT NOT NULL, ac_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, duration INT NOT NULL, price DOUBLE PRECISION DEFAULT \'0\' NOT NULL, currency VARCHAR(255) DEFAULT \'GBP\' NOT NULL, description VARCHAR(255) DEFAULT NULL, attendants_number INT DEFAULT 1 NOT NULL, INDEX IDX_3BE592D3D2E3ED2F (ac_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assessment_center_service_assessor (id INT AUTO_INCREMENT NOT NULL, ac_service_id INT DEFAULT NULL, assessor_id INT DEFAULT NULL, INDEX IDX_5A1E7965664D13D3 (ac_service_id), INDEX IDX_5A1E7965A5E4B630 (assessor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assessment_center_service ADD CONSTRAINT FK_3BE592D3D2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965664D13D3 FOREIGN KEY (ac_service_id) REFERENCES assessment_center_service (id)');
        $this->addSql('ALTER TABLE assessment_center_service_assessor ADD CONSTRAINT FK_5A1E7965A5E4B630 FOREIGN KEY (assessor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE assessment_center_service_assessor DROP FOREIGN KEY FK_5A1E7965664D13D3');
        $this->addSql('DROP TABLE assessment_center_service');
        $this->addSql('DROP TABLE assessment_center_service_assessor');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }
}
