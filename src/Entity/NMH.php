<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\String_;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NMHRepository")
 * @ApiResource
 *
 */
class NMH {

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
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $company_registered_since;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $company_reg_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $bands;

    /**
     * @ORM\Column(type="boolean", options={"defaults": "true"})
     */
    private $distance_learner;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $standard_business_hours;

    /**
     * @ORM\Column(type="boolean", options={"defaults": "true"})
     */
    private $evening_appointments;

    /**
     * @ORM\Column(type="boolean", options={"defaults": "true"})
     */
    private $weekend_appointments;

    /**
     * @var String[]
     *
     * @ORM\Column(type="simple_array")
     */
    private $regions_supplied = array();

    /**
     * @var String[]
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $institutions_survised = array();

    /**
     * Custom URL
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $admin;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return NMH
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return NMH
     */
    public function setAddress($address) {
        $this->address = $address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContactName() {
        return $this->contact_name;
    }

    /**
     * @param mixed $contact_name
     * @return NMH
     */
    public function setContactName($contact_name) {
        $this->contact_name = $contact_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     * @return NMH
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;

        return $this;
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
     * @return string
     */
    public function getCompanyRegisteredSince() {
        return $this->company_registered_since;
    }

    /**
     * @param mixed $company_registered_since
     */
    public function setCompanyRegisteredSince($company_registered_since) {
        $this->company_registered_since = $company_registered_since;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyRegNumber() {
        return $this->company_reg_number;
    }

    /**
     * @param mixed $company_reg_number
     */
    public function setCompanyRegNumber($company_reg_number) {
        $this->company_reg_number = $company_reg_number;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getBands() {
        return $this->bands;
    }

    /**
     * @param mixed $bands
     */
    public function setBands($bands) {
        $this->bands = $bands;
    }

    /**
     * @return mixed
     */
    public function getDistanceLearner() {
        return $this->distance_learner;
    }

    /**
     * @param mixed $distance_learner
     */
    public function setDistanceLearner($distance_learner) {
        $this->distance_learner = $distance_learner;
    }

    /**
     * @return mixed
     */
    public function getStandardBusinessHours() {
        return $this->standard_business_hours;
    }

    /**
     * @param mixed $standard_business_hours
     */
    public function setStandardBusinessHours($standard_business_hours) {
        $this->standard_business_hours = $standard_business_hours;
    }

    /**
     * @return mixed
     */
    public function getEveningAppointments() {
        return $this->evening_appointments;
    }

    /**
     * @param mixed $evening_appointments
     */
    public function setEveningAppointments($evening_appointments) {
        $this->evening_appointments = $evening_appointments;
    }

    /**
     * @return mixed
     */
    public function getWeekendAppointments() {
        return $this->weekend_appointments;
    }

    /**
     * @param mixed $weekend_appointments
     */
    public function setWeekendAppointments($weekend_appointments) {
        $this->weekend_appointments = $weekend_appointments;
    }

    /**
     * @return \string[]
     */
    public function getRegionsSupplied() {
        return $this->regions_supplied;
    }

    /**
     * @param \string[] $regions_supplied
     */
    public function setRegionsSupplied($regions_supplied) {
        $this->regions_supplied = $regions_supplied;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getInstitutionsSurvised() {
        return $this->institutions_survised;
    }

    /**
     * @param \string[] $institutions_survised
     */
    public function setInstitutionsSurvised($institutions_survised) {
        $this->institutions_survised = $institutions_survised;

        return $this;
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
     * @return User
     */
    public function getAdmin() {
        return $this->admin;
    }

    /**
     * @param User $admin
     */
    public function setAdmin(User $admin) {
        $this->admin = $admin;
    }

}
