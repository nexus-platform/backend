<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193145 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD assessment_centre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649944D242F FOREIGN KEY (assessment_centre_id) REFERENCES assessment_center (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649944D242F ON user (assessment_centre_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649944D242F');
        $this->addSql('DROP INDEX IDX_8D93D649944D242F ON user');
        $this->addSql('ALTER TABLE user DROP assessment_centre_id');
    }
}
