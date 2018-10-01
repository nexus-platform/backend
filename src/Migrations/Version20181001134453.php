<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181001134453 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE assessment_center_user DROP FOREIGN KEY FK_764B184DA76ED395');
        $this->addSql('ALTER TABLE assessment_center_user DROP FOREIGN KEY FK_764B184DD2E3ED2F');
        $this->addSql('ALTER TABLE assessment_center_user ADD status INT NOT NULL');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DD2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE assessment_center_user DROP FOREIGN KEY FK_764B184DD2E3ED2F');
        $this->addSql('ALTER TABLE assessment_center_user DROP FOREIGN KEY FK_764B184DA76ED395');
        $this->addSql('ALTER TABLE assessment_center_user DROP status');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DD2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }
}
