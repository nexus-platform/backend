<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UniversityDsaFormRepository")
 * @UniqueEntity(fields={"university", "dsa_form"}, message="The combination institute/form should be unique")
 * @ApiResource
 */
class UniversityDsaForm {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="University", inversedBy="univ_dsa_form")
     * @ORM\JoinColumn(name="univ_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $university;

    /**
     * @ORM\ManyToOne(targetEntity="DsaForm", inversedBy="univ_dsa_form")
     * @ORM\JoinColumn(name="dsa_form_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $dsa_form;
    
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $dsa_form_slug;
    
    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $active;
    function getId() {
        return $this->id;
    }

    function getUniversity() {
        return $this->university;
    }

    function getDsa_form() {
        return $this->dsa_form;
    }

    function getDsa_form_slug() {
        return $this->dsa_form_slug;
    }

    function getActive() {
        return $this->active;
    }
    
    function setUniversity($university) {
        $this->university = $university;
    }

    function setDsa_form($dsa_form) {
        $this->dsa_form = $dsa_form;
    }

    function setDsa_form_slug($dsa_form_slug) {
        $this->dsa_form_slug = $dsa_form_slug;
    }

    function setActive($active) {
        $this->active = $active;
    }
    
}
