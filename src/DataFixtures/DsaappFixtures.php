<?php

namespace App\DataFixtures;

use App\Entity\AssessmentCenter;
use App\Entity\Country;
use App\Entity\DisabilityOfficer;
use App\Entity\Label;
use App\Entity\User;
use App\Entity\NMH;
use App\Entity\University;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DsaappFixtures
 *
 * @author Julian Santana <juliansminf@gmail.com>
 * @package App\DataFixtures
 */
class DsaappFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadNMH($manager);
        $this->loadDOfficer($manager);
        $this->loadACenter($manager);

        // admin user
        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEmail('admin@dsaapp.uk');
        $admin->setPassword('$2y$13$umLxo6rO0.CvpmpwgaHxveoY2.tO87DyHzDUrRBPEHI3XxKzhJ4xm');

        $manager->persist($admin);
        $manager->flush();

        $url = __DIR__ . '/data/world_universities_and_domains.json';

        $contents = file_get_contents($url);
        $contents = html_entity_decode(utf8_encode($contents));
        $results = json_decode($contents, true);

        foreach ($results as $index => $result) {

            $country_str = $result['country'];

            $repo = $manager->getRepository(Country::class);
            $q = $repo->findBy(array(
                'name' => $country_str
            ));

            if (count($q) <= 0) {
                $country = new Country();
                $country->setName($country_str);
                $manager->persist($country);
                $manager->flush();
            }
        }

        foreach ($results as $index => $result) {
            $university = new University();
            $webpages = array();
            $domains = array();

            foreach ($result['web_pages'] as $index => $page) {
                $webpages[] = $page;
            }

            $university->setPages($webpages);

            $university->setName($result['name']);
            $university->setAlphaTwoCode($result['alpha_two_code']);

            foreach ($result['domains'] as $index => $domain) {
                $domains[] = $domain;
            }

            $university->setDomains($domains);

            $country_str = $result['country'];

            $repo = $manager->getRepository(Country::class);
            $q = $repo->findBy(array(
                'name' => $country_str
            ));

            $country = $q[0];

            $university->setCountry($country);

            $manager->persist($university);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadNMH(ObjectManager $manager): void
    {
        $dir = __DIR__ . "/data/nmh/";

        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '..' && $file != '.') {
                    $nmh = new NMH();

                    $contents = file_get_contents($dir . $file);
                    $contents = html_entity_decode(utf8_encode($contents));
                    $results = json_decode($contents, true);

                    //name
                    $nmh->setName($results['name']);

                    //contact name
                    $nmh->setContactName($results['contact_name']);

                    //contact email
                    $nmh->setEmail($results['email']);

                    //telephone
                    $nmh->setTelephone($results['phone']);

                    //address
                    $nmh->setAddress($results['address']);

                    //company registred since
                    $nmh->setCompanyRegisteredSince($results['company_registred_since']);

                    //company registred number
                    $nmh->setCompanyRegNumber($results['company_registred_number']);

                    //type
                    $nmh->setType($results['nmh_provider']);

                    //band_supported
                    $nmh->setBands($results['band_supported']);

                    //distance_learner
                    $nmh->setDistanceLearner($results['distance_learner'] === "Yes" ? true : false);

                    //standard_business_hours
                    $nmh->setStandardBusinessHours($results['standard_business_hours']);

                    //evening_appointments
                    $nmh->setEveningAppointments($results['evening_appointments'] === "Yes" ? true : false);

                    //weekend_appointments
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

                    $manager->persist($nmh);
                }
            }
            closedir($dh);
            $manager->flush();
        }
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadDOfficer(ObjectManager $manager): void
    {
        $dir = __DIR__ . "/data/disability-officer/";

        if ($dh = opendir($dir)) {
            $i = 0;
            while (($file = readdir($dh)) !== false) {
                //echo $i++ . $file . 's' .PHP_EOL;
                if ($file != '..' && $file != '.') {
                    $do = new DisabilityOfficer();

                    $contents = file_get_contents($dir . $file);
                    $contents = html_entity_decode(utf8_encode($contents));
                    $results = json_decode($contents, true);

                    // name
                    $do->setName($results['name']);

                    // contact name
                    $do->setContactName(trim(explode(':', $results['person'])[1]));

                    // telephone
                    $do->setTelephone(trim(explode(':', $results['phone'])[1]));

                    // address
                    $do->setAddress($results['address']);

                    // email
                    $do->setEmail($results['email']);

                    $manager->persist($do);
                }
            }
            closedir($dh);
            $manager->flush();
        }
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadACenter(ObjectManager $manager): void
    {
        $dir = __DIR__ . "/data/assessment-centre/";

        if ($dh = opendir($dir)) {
            $i = 0;
            while (($file = readdir($dh)) !== false) {
                //echo $i++ . $file . 's' .PHP_EOL;
                if ($file != '..' && $file != '.') {
                    $do = new AssessmentCenter();

                    $contents = file_get_contents($dir . $file);
                    $contents = html_entity_decode(utf8_encode($contents));
                    $results = json_decode($contents, true);

                    // name
                    $do->setName($results['name']);

                    // contact name
                    $do->setContactName(trim(explode(':', $results['person'])[1]));

                    // telephone
                    $do->setTelephone(trim(explode(':', $results['phone'])[1]));

                    // address
                    $do->setAddress($results['address']);

                    // email
                    $do->setEmail($results['email']);

                    $manager->persist($do);
                }
            }
            closedir($dh);
            $manager->flush();
        }
    }

    /**
     * @param ObjectManager $manager
     */
    protected function loadLabels(ObjectManager $manager): void
    {
        $label = new Label();
    }
}
