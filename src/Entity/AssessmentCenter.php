<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentCenterRepository")
 * @ApiResource
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
     * @var AssessmentForm[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\AssessmentForm",
     *     mappedBy="center",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $forms;

    /**
     * Custom URL
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

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
        $this->forms = new ArrayCollection();
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
    public function getForms() {
        return $this->forms;
    }

    public function addForm(AssessmentForm $form): void {
        $form->setCenter($this);
        if (!$this->forms->contains($form)) {
            $this->forms->add($form);
        }
    }

    public function removeForm(AssessmentForm $form): void {
        $form->setCenter(null);
        $this->forms->removeElement($form);
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
