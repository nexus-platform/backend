<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\EaEntityType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentCenterRepository")
 * @ApiResource
 * @UniqueEntity(fields={"url"}, message="The AC slug must be unique")
 */
class AssessmentCenter {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $contact_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $telephone;

    /**
     * @ORM\Column(type="integer", length=1)
     */
    private $automatic_booking;

    /**
     * Custom URL
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * Availability type
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $availability_type;

    /**
     * @var Label
     * @ORM\OneToMany(targetEntity="App\Entity\Label", mappedBy="center")
     */
    private $labels;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AssessmentCenterUser", mappedBy="ac")
     * */
    private $assessment_center_users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AssessmentCenterService", mappedBy="ac")
     * */
    private $assessment_center_services;

    /**
     * @var EaEntityType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EaEntityType", inversedBy="acs")
     * @ORM\JoinColumn(name="ea_entity_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $eaEntityType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nmh_company_registered_since;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nmh_company_reg_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nmh_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nmh_bands;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"defaults": "true"})
     */
    private $nmh_distance_learner = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nmh_standard_business_hours;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"defaults": "true"})
     */
    private $nmh_evening_appointments = true;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"defaults": "true"})
     */
    private $nmh_weekend_appointments = true;

    /**
     * @var String[]
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $nmh_regions_supplied = [];

    /**
     * @var String[]
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $nmh_institutions_survised = [];

    function getEaEntityType(): EaEntityType {
        return $this->eaEntityType;
    }

    function getNmh_company_registered_since() {
        return $this->nmh_company_registered_since;
    }

    function getNmh_company_reg_number() {
        return $this->nmh_company_reg_number;
    }

    function getNmh_type() {
        return $this->nmh_type;
    }

    function getNmh_bands() {
        return $this->nmh_bands;
    }

    function getNmh_distance_learner() {
        return $this->nmh_distance_learner;
    }

    function getNmh_standard_business_hours() {
        return $this->nmh_standard_business_hours;
    }

    function getNmh_evening_appointments() {
        return $this->nmh_evening_appointments;
    }

    function getNmh_weekend_appointments() {
        return $this->nmh_weekend_appointments;
    }

    function getNmh_regions_supplied(): array {
        return $this->nmh_regions_supplied;
    }

    function getNmh_institutions_survised(): array {
        return $this->nmh_institutions_survised;
    }

    function setEaEntityType(EaEntityType $eaEntityType) {
        $this->eaEntityType = $eaEntityType;
    }

    function setNmh_company_registered_since($nmh_company_registered_since) {
        $this->nmh_company_registered_since = $nmh_company_registered_since;
    }

    function setNmh_company_reg_number($nmh_company_reg_number) {
        $this->nmh_company_reg_number = $nmh_company_reg_number;
    }

    function setNmh_type($nmh_type) {
        $this->nmh_type = $nmh_type;
    }

    function setNmh_bands($nmh_bands) {
        $this->nmh_bands = $nmh_bands;
    }

    function setNmh_distance_learner($nmh_distance_learner) {
        $this->nmh_distance_learner = $nmh_distance_learner;
    }

    function setNmh_standard_business_hours($nmh_standard_business_hours) {
        $this->nmh_standard_business_hours = $nmh_standard_business_hours;
    }

    function setNmh_evening_appointments($nmh_evening_appointments) {
        $this->nmh_evening_appointments = $nmh_evening_appointments;
    }

    function setNmh_weekend_appointments($nmh_weekend_appointments) {
        $this->nmh_weekend_appointments = $nmh_weekend_appointments;
    }

    function setNmh_regions_supplied(array $nmh_regions_supplied) {
        $this->nmh_regions_supplied = $nmh_regions_supplied;
    }

    function setNmh_institutions_survised(array $nmh_institutions_survised) {
        $this->nmh_institutions_survised = $nmh_institutions_survised;
    }

    function getAutomatic_booking() {
        return $this->automatic_booking;
    }

    function setAutomatic_booking($automatic_booking) {
        $this->automatic_booking = $automatic_booking;
    }

    function getAvailability_type() {
        return $this->availability_type;
    }

    function setAvailability_type($availability_type) {
        $this->availability_type = $availability_type;
    }

    function getAssessment_center_services() {
        return $this->assessment_center_services;
    }

    function setAssessment_center_services($assessment_center_services) {
        $this->assessment_center_services = $assessment_center_services;
    }

    function getContact_name() {
        return $this->contact_name;
    }

    function getAssessment_center_users() {
        return $this->assessment_center_users;
    }

    function setContact_name($contact_name) {
        $this->contact_name = $contact_name;
    }

    function setAssessment_center_users($assessment_center_users) {
        $this->assessment_center_users = $assessment_center_users;
    }

    /**
     * AssessmentCenter constructor.
     */
    public function __construct() {
    }

    public function __toString() {
        return "Assessment Center (#" . $this->getId() . ")";
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getContactName() {
        return $this->contact_name;
    }

    /**
     * @param mixed $contact_name
     */
    public function setContactName($contact_name) {
        $this->contact_name = $contact_name;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getLabels() {
        return $this->labels;
    }

    /**
     * @param mixed $labels
     */
    public function setLabels(Label $labels) {
        $this->labels = $labels;
    }

    public function getAdmin() {
        $acUsers = $this->getAssessment_center_users();
        foreach ($acUsers as $acUser) {
            if ($acUser->getIs_admin())
                return $acUser->getUser();
        }
        return null;
    }

}
