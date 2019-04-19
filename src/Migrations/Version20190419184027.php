<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190419184027 extends AbstractMigration {

    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE file ADD ac_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD original_filename VARCHAR(255) NOT NULL, ADD new_filename VARCHAR(255) NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD description VARCHAR(255) DEFAULT NULL, DROP uid, DROP original, DROP extension');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610D2E3ED2F FOREIGN KEY (ac_id) REFERENCES assessment_center (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610D2E3ED2F');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A76ED395');
        $this->addSql('DROP INDEX IDX_8C9F3610D2E3ED2F ON file');
        $this->addSql('DROP INDEX IDX_8C9F3610A76ED395 ON file');
        $this->addSql('ALTER TABLE file ADD uid VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD original VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD extension VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP ac_id, DROP user_id, DROP original_filename, DROP new_filename, DROP name, DROP description');
    }

}
