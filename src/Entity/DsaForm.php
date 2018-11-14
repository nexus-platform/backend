<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DsaFormRepository")
 * @ApiResource
 */
class DsaForm {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $base;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $content = [];

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UniversityDsaForm", mappedBy="dsa_form")
     * */
    private $univ_dsa_form;

    function getFilledForms() {
        return $this->filledForms;
    }

    function setFilledForms($filledForms) {
        $this->filledForms = $filledForms;
    }

    function getUniv_dsa_form() {
        return $this->univ_dsa_form;
    }

    function setUniv_dsa_form($univ_dsa_form) {
        $this->univ_dsa_form = $univ_dsa_form;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent(array $content): void {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBase(): string {
        return $this->base;
    }

    /**
     * @param string $base
     */
    public function setBase(string $base) {
        $this->base = $base;
    }

    /**
     * @return mixed
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    public function getCode() {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code) {
        $this->code = $code;
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

}
