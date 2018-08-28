<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193120 extends AbstractMigration {

    public function up(Schema $schema) {
        $this->addSql("INSERT INTO `app_settings` (`mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_encryption`) VALUES ('127.0.0.1', '25', 'user', 'pass', '')");
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
    }

}
