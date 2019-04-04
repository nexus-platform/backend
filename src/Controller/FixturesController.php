<?php

namespace App\Controller;

use App\Entity\AssessmentCenter;
use App\Entity\AssessmentCenterUser;
use App\Entity\Country;
use App\Entity\DisabilityOfficer;
use App\Entity\DsaForm;
use App\Entity\EaEntityType;
use App\Entity\University;
use App\Entity\UniversityDsaForm;
use App\Entity\User;
use App\Utils\StaticMembers;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function str_split;

class FixturesController extends Controller {

    /**
     * @Route("/fixtures", name="fixtures")
     */
    public function fixtures(Request $request) {
        $entityManager = $this->getDoctrine()->getManager();

        $statement = $entityManager->getConnection()->prepare('delete from `dsa_form_filled`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('delete from `dsa_form`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('delete from `notification`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('delete from `assessment_center_service_assessor`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('delete from `user`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('delete from `university`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('delete from `assessment_center`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('delete from `debug`');
        $statement->execute();

        $originDir = $this->container->getParameter('kernel.project_dir') . '/src/DataFixtures/data';

        $res = '<i>Universities:</i> <b>' . $this->loadDOfficer($entityManager, "$originDir/disability-officer/") . '</b><br />' .
                '<i>DSA Forms:</i> <b>' . $this->loadDSAForms($entityManager, "$originDir/dsa_forms_json/") . '</b><br />' .
                '<i>Users:</i> <b>' . $this->loadUsers($entityManager) . '</b><br />' .
                '<i>Universities managed by DO:</i> <b>' . $this->loadUniversitiesForms($entityManager) . '</b><br />' .
                '<i>Assessment Centers:</i> <b>' . $this->loadACs($entityManager, "$originDir/disability-officer/") . '</b><br />' .
                '<i>Active Assessment Centers:</i> <b>' . $this->activateACs($entityManager) . '</b><br />' .
                '<i>NMHs:</i> <b>' . $this->loadNMH($entityManager) . '</b><br />'
        ;
        $acUsers = $entityManager->getRepository(AssessmentCenterUser::class)->findAll();
        foreach ($acUsers as $acUser) {
            StaticMembers::syncEaUser($entityManager, $acUser);
        }
        $entityManager->flush();
        return new Response($res);
    }

    private function loadDSAForms(ObjectManager $entityManager, $originDir) {
        $files = scandir($originDir);
        $count = 0;
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $item = new DsaForm();
                $url = "$originDir/$file";
                $item->setActive(1);
                $name = str_replace('.json', '', $file);
                $item->setName($name);
                $item->setCode($name);
                $item->setBase("$name.pdf");
                $item->setContent(json_decode(file_get_contents($url)));
                $entityManager->persist($item);
                $count++;
            }
        }
        $entityManager->flush();
        return $count;
    }

    private function loadUsers(ObjectManager $entityManager) {
        $country = $entityManager->getRepository(Country::class)->find(182);
        $universities = $entityManager->getRepository(University::class)->findBy(['country' => $country]);
        $res = '';
        $pass = sha1('a');
        $usersNeeded = 3;
        $usersCount = 0;
        $res .= ($usersNeeded - $usersCount) . ' DOs';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $university = $universities[$usersCount];
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("do$usersCount@nexus.uk");
            $user->setName("DO $usersCount");
            $user->setTelephone('+001');
            $user->setLastname('Doe');
            $user->setPassword($pass);
            $user->setRoles(["do"]);
            $user->setStatus(1);
            $user->setUniversity($university);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }

        $usersCount = 0;
        $res .= ', ' . ($usersNeeded - $usersCount) . ' Students';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("student$usersCount@nexus.uk");
            $user->setName("Student $usersCount");
            $user->setTelephone('+002');
            $user->setLastname('Lennon');
            $user->setPassword($pass);
            $user->setRoles(["student"]);
            $user->setStatus(1);
            $user->setUniversity($universities[$usersCount]);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }
        $usersCount = 0;
        $res .= ', ' . ($usersNeeded - $usersCount) . ' AC Managers';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("ac$usersCount@nexus.uk");
            $user->setName("AC $usersCount");
            $user->setTelephone('+003');
            $user->setLastname('Sparrow');
            $user->setPassword($pass);
            $user->setRoles(["ac"]);
            $user->setStatus(1);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }

        $usersCount = 0;
        $res .= ', ' . ($usersNeeded - $usersCount) . ' NAs';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("na$usersCount@nexus.uk");
            $user->setName("NA $usersCount");
            $user->setTelephone('+004');
            $user->setLastname('McCartney');
            $user->setPassword($pass);
            $user->setRoles(["na"]);
            $user->setStatus(1);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }

        //$usersCount = StaticMembers::executeRawSQL($entityManager, "SELECT count(*) as `count` FROM `user` where json_contains(roles, json_array('admin')) = 1")[0]['count'];

        $user = new User();
        $user->setCreatedAt(time());
        $user->setEmail('admin@nexus.uk');
        $user->setName("John");
        $user->setTelephone('+005');
        $user->setLastname('Snow');
        $user->setPassword($pass);
        $user->setRoles(["admin"]);
        $user->setStatus(1);
        $user->setToken(sha1(StaticMembers::random_str(32)));
        $entityManager->persist($user);
        $res .= ', 1 App Admin';

        $entityManager->flush();
        StaticMembers::executeRawSQL($entityManager, "UPDATE `user` set `password` = '$pass'", false);
        $res = trim($res, ",");
        return $res ? $res : '0';
    }

    private function loadUniversitiesForms(ObjectManager $entityManager) {
        $forms = $entityManager->getRepository(DsaForm::class)->findAll();
        $statement = $entityManager->getConnection()->prepare('DELETE FROM `university_dsa_form`');
        $statement->execute();
        $statement = $entityManager->getConnection()->prepare('SELECT DISTINCT `university_id` as `id` FROM `user` where `university_id` is not null');
        $statement->execute();
        $univs = $statement->fetchAll();
        $count = 0;
        foreach ($univs as $univ) {
            $count++;
            $univEntity = $entityManager->getRepository(University::class)->find($univ['id']);
            $univEntity->setManager($entityManager->getRepository(User::class)->findOneBy(['email' => "do$count@nexus.uk"]));
            $entityManager->persist($univEntity);
            foreach ($forms as $form) {
                $univForm = new UniversityDsaForm();
                $univForm->setDsa_form($form);
                $univForm->setUniversity($univEntity);
                $univForm->setActive(1);
                $univForm->setDsa_form_slug($form->getCode());
                $entityManager->persist($univForm);
            }
        }
        return $count;
    }

    private function activateACs(ObjectManager $entityManager) {
        $acs = $entityManager->getRepository(AssessmentCenter::class)->findAll();
        $count = 0;
        if ($acs) {
            while ($count < 3) {
                $count++;
                //Setting the AC manager
                $user = $entityManager->getRepository(User::class)->findOneBy(['email' => "ac$count@nexus.uk"]);
                if ($user) {
                    $ac = $acs[$count];
                    $acu = new AssessmentCenterUser();
                    $acu->setAc($ac);
                    $acu->setIs_admin(1);
                    $acu->setStatus(1);
                    $acu->setUser($user);
                    $entityManager->persist($acu);
                }
                $i = 0;
                while ($i < $count) {
                    $i++;
                    //Setting the NAs
                    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => "na$i@nexus.uk"]);
                    if ($user) {
                        $ac = $acs[$count];
                        $acu = new AssessmentCenterUser();
                        $acu->setAc($ac);
                        $acu->setIs_admin(0);
                        $acu->setStatus(1);
                        $acu->setUser($user);
                        $entityManager->persist($acu);
                    }
                    //Setting the students
                    $user = $entityManager->getRepository(User::class)->findOneBy(['email' => "student$i@nexus.uk"]);
                    if ($user) {
                        $ac = $acs[$count];
                        $acu = new AssessmentCenterUser();
                        $acu->setAc($ac);
                        $acu->setIs_admin(0);
                        $acu->setStatus(1);
                        $acu->setUser($user);
                        $entityManager->persist($acu);
                    }
                }
            }
        }
        $entityManager->flush();

        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "company_working_plan" as `name`, \'{"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}\' as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "book_advance_timeout" as `name`, "30" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "company_name" as `name`, (select `name` from `assessment_center` where `id` = `acu`.`ac_id`) as `value` from `assessment_center_user` `acu` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "google_analytics_code" as `name`, "" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "company_email" as `name`, (select email from user where id = `acu`.`user_id`) as `value` from `assessment_center_user` `acu` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "company_link" as `name`, (select `url` from `assessment_center` where `id` = `acu`.`ac_id`) as `value` from `assessment_center_user` `acu` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "customer_notifications" as `name`, "1" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "date_format" as `name`, "DMY" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "time_format" as `name`, "regular" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "require_captcha" as `name`, "0" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "display_cookie_notice" as `name`, "0" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "cookie_notice_content" as `name`, "Cookie notice content." as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "display_terms_and_conditions" as `name`, "0" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "terms_and_conditions_content" as `name`, "Terms and conditions content." as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "display_privacy_policy" as `name`, "0" as `value` from `assessment_center_user` where `is_admin` = 1', false);
        StaticMembers::executeRawSQL($entityManager, 'insert into `ea_settings` (`id_assessment_center`, `name`, `value`) select distinct `ac_id`, "privacy_policy_content" as `name`, "Privacy policy content." as `value` from `assessment_center_user` where `is_admin` = 1', false);

        return $count;
    }

    private function loadACs(ObjectManager $entityManager, $dir) {
        $count = 0;

        if ($dh = opendir($dir)) {
            $i = 0;
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    $contents = file_get_contents($dir . $file);
                    $contents = html_entity_decode(utf8_encode($contents));
                    $results = json_decode($contents, true);
                    $entity = $entityManager->getRepository(AssessmentCenter::class)->findOneBy(['name' => $results['name']]);
                    if (!$entity) {
                        $eaEntityType = $entityManager->getRepository(EaEntityType::class)->find(1);
                        $ac = new AssessmentCenter();
                        $ac->setEaEntityType($eaEntityType);
                        $ac->setName($results['name']);
                        $ac->setContactName(trim(explode(':', $results['person'])[1]));
                        $ac->setTelephone(trim(explode(':', $results['phone'])[1]));
                        $ac->setAddress($results['address']);
                        $ac->setEmail($results['email']);
                        $ac->setUrl($this->alphabeticString($results['name']));
                        $ac->setAutomatic_booking(0);

                        $ac->setNmh_distance_learner(false);
                        $ac->setNmh_evening_appointments(false);
                        $ac->setNmh_weekend_appointments(false);
                        $ac->setNmh_regions_supplied([""]);
                        $entityManager->persist($ac);
                        $count++;
                    }
                }
            }
            closedir($dh);
        }
        $entityManager->flush();
        return $count;
    }

    private function alphabeticString($string) {
        $words = explode(' ', strtolower($string));
        $wordsCount = count($words);
        for ($i = 0; $i < $wordsCount; $i++) {
            $chars = str_split($words[$i]);
            $charsCount = count($chars);
            for ($j = 0; $j < $charsCount; $j++) {
                $ascii = ord($chars[$j]);
                if (!(($ascii > 47 && $ascii < 58) || ($ascii > 96 && $ascii < 123))) {
                    unset($chars[$j]);
                }
            }
            $charsToStr = implode('', $chars);
            if ($charsToStr !== '') {
                $words[$i] = $charsToStr;
            } else {
                unset($words[$i]);
            }
        }
        return implode('-', $words);
    }

    private function loadNMH(ObjectManager $entityManager) {
        $dir = $this->container->getParameter('kernel.project_dir') . '/src/DataFixtures/data/nmh/';
        $count = 0;

        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    $eaEntityType = $entityManager->getRepository(EaEntityType::class)->find(2);
                    $nmh = new AssessmentCenter();
                    $nmh->setEaEntityType($eaEntityType);

                    $contents = file_get_contents($dir . $file);
                    $contents = html_entity_decode(utf8_encode($contents));
                    $results = json_decode($contents, true);

                    $nmh->setName($results['name']);
                    $nmh->setContactName($results['contact_name']);
                    $nmh->setEmail($results['email']);
                    $nmh->setTelephone($results['phone']);
                    $nmh->setAddress($results['address']);
                    $nmh->setAutomatic_booking(0);
                    $nmh->setNmh_company_registered_since($results['company_registred_since']);
                    $nmh->setNmh_company_reg_number($results['company_registred_number']);
                    $nmh->setNmh_type($results['nmh_provider']);
                    $nmh->setNmh_bands($results['band_supported']);
                    $nmh->setNmh_distance_learner($results['distance_learner'] === "Yes" ? true : false);
                    $nmh->setNmh_standard_business_hours($results['standard_business_hours']);
                    $nmh->setNmh_evening_appointments($results['evening_appointments'] === "Yes" ? true : false);
                    $nmh->setNmh_weekend_appointments($results['weekend_appointments'] === "Yes" ? true : false);
                    //regions_supplied
                    $rs = array();
                    foreach ($results['regions_supplied'] as $value) {
                        $rs[] = $value;
                    }
                    $nmh->setNmh_regions_supplied($rs);
                    //institutions_serviced
                    $is = array();
                    foreach ($results['institutions_serviced'] as $value) {
                        $is[] = $value;
                    }
                    $nmh->setNmh_institutions_survised($is);
                    $entityManager->persist($nmh);
                    $count ++;
                }
            }
            closedir($dh);
        }
        return $count;
    }

    private function loadDOfficer(ObjectManager $entityManager, $dir) {
        $count = 0;
        if ($dh = opendir($dir)) {
            $country = $entityManager->getRepository(Country::class)->find(182);

            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    //$contents = file_get_contents($dir . $file);
                    //$contents = html_entity_decode(utf8_encode(file_get_contents($dir . $file)));
                    $results = json_decode(html_entity_decode(utf8_encode(file_get_contents($dir . $file))), true);

                    $univ = new University();
                    $univ->setCountry($country);
                    $univ->setName($results['name']);
                    $univ->setToken($this->alphabeticString($results['name']));
                    $univ->setDomains([$univ->getToken()]);
                    $univ->setPages($univ->getDomains());
                    $entityManager->persist($univ);

                    if (!$results['email'] || !$entityManager->getRepository(DisabilityOfficer::class)->findOneBy(['email' => $results['email']])) {
                        $do = new DisabilityOfficer();
                        $do->setName($results['name']);
                        $do->setContactName(trim(explode(':', $results['person'])[1]));
                        $do->setTelephone(trim(explode(':', $results['phone'])[1]));
                        $do->setAddress($results['address']);
                        $do->setEmail($results['email']);
                        $entityManager->persist($do);
                    }

                    $count++;
                }
            }
            closedir($dh);
            $entityManager->flush();
        }
        return $count;
    }

}
