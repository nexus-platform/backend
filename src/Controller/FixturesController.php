<?php

namespace App\Controller;

use App\Entity\AssessmentCenter;
use App\Entity\AssessmentCenterUser;
use App\Entity\Country;
use App\Entity\DisabilityOfficer;
use App\Entity\DsaForm;
use App\Entity\NMH;
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
        $originDir = $this->container->getParameter('kernel.project_dir') . '/src/DataFixtures/data';
        $res = '<i>Loaded DSA Forms:</i> <b>' . $this->loadDSAForms($entityManager, "$originDir/dsa_forms_json") . '</b><br />' .
                '<i>Users added:</i> <b>' . $this->loadUsers($entityManager) . '</b><br />' .
                '<i>Universities managed by DO:</i> <b>' . $this->loadUniversitiesForms($entityManager) . '</b><br />' .
                '<i>New Assessment Centers:</i> <b>' . $this->loadACs($entityManager) . '</b><br />' .
                '<i>New Active Assessment Centers:</i> <b>' . $this->activateACs($entityManager) . '</b><br />'
        ;
        $entityManager->flush();
        return new Response($res);
    }

    private function loadDSAForms(ObjectManager $entityManager, $originDir) {
        $files = scandir($originDir);
        $count = 0;
        foreach ($files as $file) {
            $item = $entityManager->getRepository(DsaForm::class)->findOneBy(['code' => str_replace('.json', '', $file)]);
            if ($item) {
                $url = "$originDir/$file";
                $item->setContent(json_decode(file_get_contents($url)));
                $entityManager->persist($item);
                $count++;
            }
        }
        return $count;
    }

    private function loadUsers(ObjectManager $entityManager) {
        $country = $entityManager->getRepository(Country::class)->find(182);
        $universities = $entityManager->getRepository(University::class)->findBy(['country' => $country]);
        $res = '';
        $pass = sha1('a');
        $usersNeeded = 3;
        $usersCount = StaticMembers::executeRawSQL($entityManager, "SELECT count(*) as `count` FROM `user` where json_contains(roles, json_array('do')) = 1")[0]['count'];
        $res .= ($usersNeeded - $usersCount) . ' DOs';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("do$usersCount@nexus.uk");
            $user->setName("DO $usersCount");
            $user->setLastname('Doe');
            $user->setPassword($pass);
            $user->setRoles(["do"]);
            $user->setStatus(1);
            $user->setUniversity($universities[$usersCount]);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }

        $usersCount = StaticMembers::executeRawSQL($entityManager, "SELECT count(*) as `count` FROM `user` where json_contains(roles, json_array('student')) = 1")[0]['count'];
        $res .= ', ' . ($usersNeeded - $usersCount) . ' Students';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("student$usersCount@nexus.uk");
            $user->setName("Student $usersCount");
            $user->setLastname('Lennon');
            $user->setPassword($pass);
            $user->setRoles(["student"]);
            $user->setStatus(1);
            $user->setUniversity($universities[$usersCount]);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }

        $usersCount = StaticMembers::executeRawSQL($entityManager, "SELECT count(*) as `count` FROM `user` where json_contains(roles, json_array('ac')) = 1")[0]['count'];
        $res .= ', ' . ($usersNeeded - $usersCount) . ' AC Managers';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("ac$usersCount@nexus.uk");
            $user->setName("AC $usersCount");
            $user->setLastname('Sparrow');
            $user->setPassword($pass);
            $user->setRoles(["ac"]);
            $user->setStatus(1);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }

        $usersCount = StaticMembers::executeRawSQL($entityManager, "SELECT count(*) as `count` FROM `user` where json_contains(roles, json_array('na')) = 1")[0]['count'];
        $res .= ', ' . ($usersNeeded - $usersCount) . ' NAs';
        while ($usersCount < $usersNeeded) {
            $usersCount++;
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail("na$usersCount@nexus.uk");
            $user->setName("NA $usersCount");
            $user->setLastname('McCartney');
            $user->setPassword($pass);
            $user->setRoles(["na"]);
            $user->setStatus(1);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
        }

        $usersCount = StaticMembers::executeRawSQL($entityManager, "SELECT count(*) as `count` FROM `user` where json_contains(roles, json_array('admin')) = 1")[0]['count'];
        if ($usersCount < 1) {
            $user = new User();
            $user->setCreatedAt(time());
            $user->setEmail('admin@nexus.uk');
            $user->setName("John");
            $user->setLastname('Snow');
            $user->setPassword($pass);
            $user->setRoles(["admin"]);
            $user->setStatus(1);
            $user->setToken(sha1(StaticMembers::random_str(32)));
            $entityManager->persist($user);
            $res .= ', 1 App Admin';
        }
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
            $univEntity = $entityManager->getRepository(University::class)->find($univ['id']);
            $univEntity->setManager($entityManager->getRepository(User::class)->findOneBy(['email' => 'do@nexus.uk']));
            $entityManager->persist($univEntity);
            foreach ($forms as $form) {
                $univForm = new UniversityDsaForm();
                $univForm->setDsa_form($form);
                $univForm->setUniversity($univEntity);
                $univForm->setActive(1);
                $univForm->setDsa_form_slug($form->getCode());
                $entityManager->persist($univForm);
            }
            $count++;
        }
        return $count;
    }

    private function activateACs(ObjectManager $entityManager) {
        $entities = $entityManager->getRepository(AssessmentCenterUser::class)->getActiveACs();
        $acs = $entityManager->getRepository(AssessmentCenter::class)->findAll();
        $res = 0;
        if ($acs) {
            $count = count($entities);
            $res = 3 - $count;
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
        return $res;
    }

    private function loadACs(ObjectManager $entityManager) {
        $dir = $this->container->getParameter('kernel.project_dir') . '/src/DataFixtures/data//assessment-centre/';
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
                        $ac = new AssessmentCenter();
                        $ac->setName($results['name']);
                        $ac->setContactName(trim(explode(':', $results['person'])[1]));
                        $ac->setTelephone(trim(explode(':', $results['phone'])[1]));
                        $ac->setAddress($results['address']);
                        $ac->setEmail($results['email']);
                        $url = $this->alphabeticString($results['name']);
                        $ac->setUrl($url);
                        $entityManager->persist($ac);
                        $count++;
                    }
                }
            }
            closedir($dh);
        }
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
        $statement = $entityManager->getConnection()->prepare('DELETE FROM `nmh`');
        $statement->execute();

        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    $nmh = new NMH();

                    $contents = file_get_contents($dir . $file);
                    $contents = html_entity_decode(utf8_encode($contents));
                    $results = json_decode($contents, true);

                    $nmh->setName($results['name']);
                    $nmh->setContactName($results['contact_name']);
                    $nmh->setEmail($results['email']);
                    $nmh->setTelephone($results['phone']);
                    $nmh->setAddress($results['address']);
                    $nmh->setCompanyRegisteredSince($results['company_registred_since']);
                    $nmh->setCompanyRegNumber($results['company_registred_number']);
                    $nmh->setType($results['nmh_provider']);
                    $nmh->setBands($results['band_supported']);
                    $nmh->setDistanceLearner($results['distance_learner'] === "Yes" ? true : false);
                    $nmh->setStandardBusinessHours($results['standard_business_hours']);
                    $nmh->setEveningAppointments($results['evening_appointments'] === "Yes" ? true : false);
                    $nmh->setWeekendAppointments($results['weekend_appointments'] === "Yes" ? true : false);
                    //regions_supplied
                    $rs = array();
                    foreach ($results['regions_supplied'] as $value) {
                        $rs[] = $value;
                    }
                    $nmh->setRegionsSupplied($rs);
                    //institutions_serviced
                    $is = array();
                    foreach ($results['institutions_serviced'] as $value) {
                        $is[] = $value;
                    }
                    $nmh->setInstitutionsSurvised($is);
                    $entityManager->persist($nmh);
                }
            }
            closedir($dh);
        }
    }

    private function loadDOfficer(ObjectManager $entityManager) {
        $dir = $this->container->getParameter('kernel.project_dir') . '/src/DataFixtures/data/disability-officer/';
        if ($dh = opendir($dir)) {
            $statement = $entityManager->getConnection()->prepare('DELETE FROM `disability_officer`');
            $statement->execute();
            $statement = $entityManager->getConnection()->prepare('DELETE FROM `university`');
            $statement->execute();
            $i = 0;
            $country = $entityManager->getRepository(Country::class)->find(182);

            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {

                    //$contents = file_get_contents($dir . $file);
                    //$contents = html_entity_decode(utf8_encode(file_get_contents($dir . $file)));
                    $results = json_decode(html_entity_decode(utf8_encode(file_get_contents($dir . $file))), true);

                    $univ = new University();
                    $univ->setCountry($country);
                    $univ->setName($results['name']);
                    $univ->setToken(StaticMembers::random_str(32));
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

                    /* $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $results['email']]);
                      if ($results['email'] != '' && !$user) {
                      $user = new User();
                      $user->setCreatedAt(time());
                      $user->setEmail($results['email']);
                      $user->setName(trim(explode(':', $results['person'])[1]));
                      $user->setPassword(sha1('Pass123*'));
                      $user->setRoles(["do"]);
                      $user->setStatus(1);
                      $user->setToken(sha1(StaticMembers::random_str(32)));
                      $entityManager->persist($user);
                      } */
                }
            }
            closedir($dh);
        }
    }

}
