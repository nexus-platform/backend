<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193146 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE assessment_center_user (id INT AUTO_INCREMENT NOT NULL, ac_id INT DEFAULT NULL, user_id INT DEFAULT NULL, is_admin INT NOT NULL, INDEX IDX_764B184DD2E3ED2F (ac_id), INDEX IDX_764B184DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DD2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id)');
        $this->addSql('ALTER TABLE assessment_center_user ADD CONSTRAINT FK_764B184DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE assessment_center DROP FOREIGN KEY FK_B42D7A4AA76ED395');
        $this->addSql('DROP INDEX UNIQ_B42D7A4AA76ED395 ON assessment_center');
        $this->addSql('ALTER TABLE assessment_center DROP user_id');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649944D242F');
        $this->addSql('DROP INDEX IDX_8D93D649944D242F ON user');
        $this->addSql('ALTER TABLE user DROP assessment_centre_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE assessment_center_user');
        $this->addSql('ALTER TABLE assessment_center ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE assessment_center ADD CONSTRAINT FK_B42D7A4AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B42D7A4AA76ED395 ON assessment_center (user_id)');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD assessment_centre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649944D242F FOREIGN KEY (assessment_centre_id) REFERENCES assessment_center (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649944D242F ON user (assessment_centre_id)');
    }
}
