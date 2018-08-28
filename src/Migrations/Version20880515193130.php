<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193130 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE qr_code ADD student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE qr_code ADD CONSTRAINT FK_7D8B1FB5CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7D8B1FB5CB944F1A ON qr_code (student_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE qr_code DROP FOREIGN KEY FK_7D8B1FB5CB944F1A');
        $this->addSql('DROP INDEX IDX_7D8B1FB5CB944F1A ON qr_code');
        $this->addSql('ALTER TABLE qr_code DROP student_id');
    }
}
