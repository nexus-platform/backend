<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193139 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE university_dsa_form (id INT AUTO_INCREMENT NOT NULL, univ_id INT DEFAULT NULL, dsa_form_id INT DEFAULT NULL, dsa_form_slug VARCHAR(255) NOT NULL, active INT NOT NULL, INDEX IDX_5E393F8252B4B886 (univ_id), INDEX IDX_5E393F8293705A43 (dsa_form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8252B4B886 FOREIGN KEY (univ_id) REFERENCES university (id)');
        $this->addSql('ALTER TABLE university_dsa_form ADD CONSTRAINT FK_5E393F8293705A43 FOREIGN KEY (dsa_form_id) REFERENCES dsa_form (id)');
        $this->addSql('ALTER TABLE dsa_form DROP parameters');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE university_dsa_form');
        $this->addSql('ALTER TABLE dsa_form ADD parameters JSON NOT NULL');
        $this->addSql('ALTER TABLE nmh CHANGE distance_learner distance_learner TINYINT(1) NOT NULL, CHANGE evening_appointments evening_appointments TINYINT(1) NOT NULL, CHANGE weekend_appointments weekend_appointments TINYINT(1) NOT NULL');
    }
}
