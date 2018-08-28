<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country {

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
     * @ORM\Column(type="string", length=5, nullable=false)
     */
    private $alpha_two_code;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getAlphaTwoCode() {
        return $this->alpha_two_code;
    }
    
    public function setAlphaTwoCode($code) {
        $this->alpha_two_code = $code;
    }

    public function __toString(): string {
        return $this->getName();
    }

}
