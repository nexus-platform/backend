<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use App\Utils\StaticMembers;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180925141344 extends AbstractMigration {

    public function up(Schema $schema): void {
        //Default App Administrator
        $pass = sha1('a');
        $this->addSql("INSERT INTO `user` (`name`, `lastname`, `roles`, `email`, `password`, `status`, `token`, `created_at`) VALUES ('Admin', 'Istrator', '[\"admin\"]', 'admin@nexus.uk', '$pass', 1, '" . StaticMembers::random_str(16) . "', '" . time() . "')");

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

        //App Settings
        $this->addSql("INSERT INTO `app_settings` (`mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_encryption`) VALUES ('127.0.0.1', '25', 'user@server.dom', 'pass', '')");

        //Countries
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (1, 'Albania', 'AL')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (2, 'Algeria', 'DZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (3, 'Andorra', 'AD')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (4, 'Angola', 'AO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (5, 'Antigua and Barbuda', 'AG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (6, 'Argentina', 'AR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (7, 'Armenia', 'AM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (8, 'Australia', 'AU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (9, 'Austria', 'AT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (10, 'Azerbaijan', 'AZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (11, 'Bahamas', 'BS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (12, 'Bahrain', 'BH')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (13, 'Bangladesh', 'BD')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (14, 'Barbados', 'BB')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (15, 'Belarus', 'BY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (16, 'Belgium', 'BE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (17, 'Belize', 'BZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (18, 'Benin', 'BJ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (19, 'Bhutan', 'BT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (20, 'Bolivia, Plurinational State of', 'BO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (21, 'Bosnia and Herzegovina', 'BA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (22, 'Botswana', 'BW')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (23, 'Brazil', 'BR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (24, 'Brunei Darussalam', 'BN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (25, 'Bulgaria', 'BG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (26, 'Burkina Faso', 'BF')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (27, 'Burundi', 'BI')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (28, 'Cabo Verde', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (29, 'Cambodia', 'KH')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (30, 'Cameroon', 'CM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (31, 'Canada', 'CA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (32, 'Central African Republic', 'CF')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (33, 'Chad', 'TD')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (34, 'Chile', 'CL')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (35, 'China', 'CN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (36, 'Colombia', 'CO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (37, 'Comoros', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (38, 'Congo, the Democratic Republic of the', 'CD')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (39, 'Costa Rica', 'CR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (40, 'Côte d\'Ivoire', 'CI')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (41, 'Croatia', 'HR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (42, 'Cuba', 'CU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (43, 'Cyprus', 'CY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (44, 'Czech Republic', 'CZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (45, 'Korea, Democratic People\'s Republic of', 'KP')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (47, 'Denmark', 'DK')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (48, 'Djibouti', 'DJ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (49, 'Dominica', 'DM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (50, 'Dominican Republic', 'DO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (51, 'Ecuador', 'EC')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (52, 'Egypt', 'EG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (53, 'El Salvador', 'SV')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (54, 'Equatorial Guinea', 'GQ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (55, 'Eritrea', 'ER')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (56, 'Estonia', 'EE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (57, 'Ethiopia', 'ET')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (58, 'Eswatini', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (59, 'Fiji', 'FJ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (60, 'Finland', 'FI')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (61, 'France', 'FR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (62, 'Gabon', 'GA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (63, 'Gambia', 'GM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (64, 'Georgia', 'GE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (65, 'Germany', 'DE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (66, 'Ghana', 'GH')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (67, 'Greece', 'GR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (68, 'Grenada', 'GD')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (69, 'Guatemala', 'GT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (70, 'Guinea', 'GN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (71, 'Guinea-Bissau', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (72, 'Guyana', 'GY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (73, 'Haiti', 'HT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (74, 'Honduras', 'HN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (75, 'Hungary', 'HU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (76, 'Iceland', 'IS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (77, 'India', 'IN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (78, 'Indonesia', 'ID')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (79, 'Iran, Islamic Republic of', 'IR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (80, 'Iraq', 'IQ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (81, 'Ireland', 'IE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (82, 'Israel', 'IL')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (83, 'Italy', 'IT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (84, 'Jamaica', 'JM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (85, 'Japan', 'JP')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (86, 'Jordan', 'JO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (87, 'Kazakhstan', 'KZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (88, 'Kenya', 'KE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (89, 'Kiribati', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (90, 'Kuwait', 'KW')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (91, 'Kyrgyzstan', 'KG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (92, 'Lao People\'s Democratic Republic', 'LA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (93, 'Latvia', 'LV')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (94, 'Lebanon', 'LB')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (95, 'Lesotho', 'LS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (96, 'Liberia', 'LR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (97, 'Libya', 'LY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (98, 'Liechtenstein', 'LI')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (99, 'Lithuania', 'LT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (100, 'Luxembourg', 'LU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (101, 'Madagascar', 'MG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (102, 'Malawi', 'MW')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (103, 'Malaysia', 'MY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (104, 'Maldives', 'MV')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (105, 'Mali', 'ML')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (106, 'Malta', 'MT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (107, 'Marshall Islands', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (108, 'Mauritania', 'MR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (109, 'Mauritius', 'MU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (110, 'Mexico', 'MX')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (111, 'Federated States of Micronesia', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (112, 'Monaco', 'MC')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (113, 'Mongolia', 'MN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (114, 'Montenegro', 'ME')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (115, 'Morocco', 'MA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (116, 'Mozambique', 'MZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (117, 'Myanmar', 'MM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (118, 'Namibia', 'NA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (119, 'Nauru', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (120, 'Nepal', 'NP')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (121, 'Netherlands', 'NL')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (122, 'New Zealand', 'NZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (123, 'Nicaragua', 'NI')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (124, 'Niger', 'NE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (125, 'Nigeria', 'NG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (126, 'Norway', 'NO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (127, 'Oman', 'OM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (128, 'Pakistan', 'PK')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (129, 'Palau', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (130, 'Panama', 'PA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (131, 'Papua New Guinea', 'PG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (132, 'Paraguay', 'PY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (133, 'Peru', 'PE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (134, 'Philippines', 'PH')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (135, 'Poland', 'PL')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (136, 'Portugal', 'PT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (137, 'Qatar', 'QA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (138, 'Korea, Republic of', 'KR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (139, 'Moldova, Republic of', 'MD')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (140, 'Romania', 'RO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (141, 'Russian Federation', 'RU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (142, 'Rwanda', 'RW')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (143, 'Saint Kitts and Nevis', 'KN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (144, 'Saint Lucia', 'LC')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (145, 'Saint Vincent and the Grenadines', 'VC')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (146, 'Samoa', 'WS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (147, 'San Marino', 'SM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (148, 'São Tomé and Príncipe', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (149, 'Saudi Arabia', 'SA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (150, 'Senegal', 'SN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (151, 'Serbia', 'RS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (152, 'Seychelles', 'SC')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (153, 'Sierra Leone', 'SL')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (154, 'Singapore', 'SG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (155, 'Slovakia', 'SK')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (156, 'Slovenia', 'SI')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (157, 'Solomon Islands', 'SB')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (158, 'Somalia', 'SO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (159, 'South Africa', 'ZA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (160, 'South Sudan', 'SS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (161, 'Spain', 'ES')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (162, 'Sri Lanka', 'LK')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (163, 'Sudan', 'SD')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (164, 'Suriname', 'SR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (165, 'Sweden', 'SE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (166, 'Switzerland', 'CH')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (167, 'Syrian Arab Republic', 'SY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (168, 'Tajikistan', 'TJ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (169, 'Thailand', 'TH')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (170, 'Macedonia, the Former Yugoslav Republic of', 'MK')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (171, 'Timor-Leste', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (172, 'Togo', 'TG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (173, 'Tonga', 'TO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (174, 'Trinidad and Tobago', 'TT')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (175, 'Tunisia', 'TN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (176, 'Turkey', 'TR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (177, 'Turkmenistan', 'TM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (178, 'Tuvalu', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (179, 'Uganda', 'UG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (180, 'Ukraine', 'UA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (181, 'United Arab Emirates', 'AE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (182, 'United Kingdom', 'GB')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (183, 'Tanzania, United Republic of', 'TZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (184, 'United States of America', 'US')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (185, 'Uruguay', 'UY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (186, 'Uzbekistan', 'UZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (187, 'Vanuatu', '00')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (188, 'Venezuela, Bolivarian Republic of', 'VE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (189, 'Viet Nam', 'VN')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (190, 'Yemen', 'YE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (191, 'Zambia', 'ZM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (192, 'Zimbabwe', 'ZW')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (193, 'Afghanistan', 'AF')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (194, 'Bermuda', 'BM')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (195, 'Cape Verde', 'CV')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (196, 'Cayman Islands', 'KY')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (197, 'Faroe Islands', 'FO')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (198, 'French Guiana', 'GF')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (199, 'French Polynesia', 'PF')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (201, 'Greenland', 'GL')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (202, 'Guadeloupe', 'GP')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (203, 'Guam', 'GU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (204, 'Holy See (Vatican City State)', 'VA')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (205, 'Hong Kong', 'HK')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (206, 'Kosovo', 'XK')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (207, 'Martinique', 'MQ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (208, 'Montserrat', 'MS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (209, 'New Caledonia', 'NC')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (210, 'Niue', 'NU')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (211, 'Palestine, State of', 'PS')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (212, 'Puerto Rico', 'PR')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (213, 'Réunion', 'RE')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (214, 'Swaziland', 'SZ')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (215, 'Taiwan, Province of China', 'TW')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (216, 'Turks and Caicos Islands', 'TC')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (217, 'Virgin Islands, British', 'VG')");
        $this->addSql("INSERT INTO `country` (`id`, `name`, `alpha_two_code`) VALUES (218, 'Macao', 'MO')");
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
    }

}
