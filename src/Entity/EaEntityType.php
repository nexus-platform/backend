<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EaEntityTypeRepository")
 * @ApiResource
 */
class EaEntityType {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $name;

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->name = $name;
    }

    /**
     * @var AssessmentCenter
     * @ORM\OneToMany(targetEntity="App\Entity\AssessmentCenter", mappedBy="entityType")
     */
    private $acs;

    function getAcs(): AssessmentCenter {
        return $this->acs;
    }

}
