<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentCenterServiceRepository")
 * @ApiResource
 */
class AssessmentCenterService {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AssessmentCenter", inversedBy="assessment_center_services")
     * @ORM\JoinColumn(name="ac_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $ac;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duration;

    /**
     * @ORM\Column(type="float", nullable=false, options={"default" : 0})
     */
    private $price;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default" : "GBP"})
     */
    private $currency;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 1})
     */
    private $attendants_number;

    function getId() {
        return $this->id;
    }

    function getAc() {
        return $this->ac;
    }

    function getName() {
        return $this->name;
    }

    function getDuration() {
        return $this->duration;
    }

    function getPrice() {
        return $this->price;
    }

    function getCurrency() {
        return $this->currency;
    }

    function getDescription() {
        return $this->description;
    }

    function getAttendants_number() {
        return $this->attendants_number;
    }

    function setAc($ac) {
        $this->ac = $ac;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDuration($duration) {
        $this->duration = $duration;
    }

    function setPrice($price) {
        $this->price = $price;
    }

    function setCurrency($currency) {
        $this->currency = $currency;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setAttendants_number($attendants_number) {
        $this->attendants_number = $attendants_number;
    }

}
