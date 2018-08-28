<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use App\Utils\StaticMembers;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20880515193119 extends AbstractMigration {

    public function up(Schema $schema) {
        //Default App Administrator
        $pass = sha1('a');
        $token = sha1('1');
        $this->addSql("INSERT INTO `user` (`name`, `lastname`, `roles`, `email`, `password`, `status`, `token`, `created_at`) VALUES ('Admin', 'Istrator', '[\"administrator\"]', 'admin@nexus.co.uk', '$pass', 1, '$token', '" . time() . "')");

        //PDF Forms
        $this->addSql("INSERT INTO `dsa_form` (`id`, `name`, `code`, `base`, `active`, `content`) VALUES
            (1, 'Claim for reimbursement of costs through Disabled Students’ Allowances 2017/18', 'sfe_dsa_costs_claim_form_1718_d', 'sfe_dsa_costs_claim_form_1718_d.pdf', 1, '{}'),
            (2, 'Claim for reimbursement of costs through Disabled Students’ Allowances 2018/19', 'sfe_dsa_costs_claim_form_1819_o', 'sfe_dsa_costs_claim_form_1819_o.pdf', 1, '{}'),
            (3, 'Disabled Students’ Allowances (DSAs) Disability Evidence Form', 'sfe_dsa_disability_evidence_form_1819_o', 'sfe_dsa_disability_evidence_form_1819_o.pdf', 1, '{}'),
            (4, 'Request for temporary Disabled Student Allowances support form', 'sfe_dsa_request_for_temporary_support_form_d', 'sfe_dsa_request_for_temporary_support_form_d.pdf', 1, '{}'),
            (5, 'DSA SLIM 2017/18 - Disabled Students’ Allowances Application Form', 'sfe_dsa_slim_form_1718_d', 'sfe_dsa_slim_form_1718_d.pdf', 1, '{}'),
            (6, 'DSA SLIM 2018/19 - Disabled Students’ Allowances Application Form', 'sfe_dsa_slim_form_1819_o', 'sfe_dsa_slim_form_1819_o.pdf', 1, '{}'),
            (7, 'DSA1 2017/18 - Application for Disabled Students’ Allowances', 'sfe_dsa1_form_1718_d', 'sfe_dsa1_form_1718_d.pdf', 1, '{}'),
            (8, 'DSA1 2018/19 - Application for Disabled Students’ Allowances', 'sfe_dsa1_form_1819_o', 'sfe_dsa1_form_1819_o.pdf', 1, '{}')");
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
    }

}
