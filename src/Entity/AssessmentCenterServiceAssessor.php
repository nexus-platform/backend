<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentCenterServiceAssessorRepository")
 * @ApiResource
 */
class AssessmentCenterServiceAssessor {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AssessmentCenterService", inversedBy="assessment_center_services")
     * @ORM\JoinColumn(name="ac_service_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assessment_center_services_assessors")
     * @ORM\JoinColumn(name="assessor_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $assessor;

    function getId() {
        return $this->id;
    }

    function getService() {
        return $this->service;
    }

    function getAssessor() {
        return $this->assessor;
    }

    function setService($service) {
        $this->service = $service;
    }

    function setAssessor($assessor) {
        $this->assessor = $assessor;
    }

}
