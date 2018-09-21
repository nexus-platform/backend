<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(iri="http://bib.schema.org/University")
 *
 * @ORM\Entity(repositoryClass="App\Repository\UniversityRepository")
 *
 * @author Julian Santana <juliansminf@gmail.com>
 */
class University {

    /**
     * @ApiProperty(
     *     iri="http://schema.org/identifier"
     * )
     *
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
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state_province;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", cascade={"persist"})
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     *
     * @Assert\NotBlank
     */
    private $country;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", onDelete="SET NULL")
     *
     */
    private $manager;

    /**
     * List of domains associated to the university.
     *
     * @var string[]
     * @ORM\Column(type="simple_array")
     *
     * @Assert\NotBlank
     */
    private $domains = array();

    /**
     * List of web pages associated to the university.
     *
     * @var string[]
     * @ORM\Column(type="simple_array")
     *
     * @Assert\NotBlank
     */
    private $pages = array();

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UniversityDsaForm", mappedBy="university")
     * */
    private $univ_dsa_form;

    function getManager() {
        return $this->manager;
    }

    function setManager($manager) {
        $this->manager = $manager;
    }

    function getUniv_dsa_form() {
        return $this->univ_dsa_form;
    }

    function setUniv_dsa_form($univ_dsa_form) {
        $this->univ_dsa_form = $univ_dsa_form;
    }

    /**
     * id can be null until flush is done
     * @return int
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
     * @return University
     */
    public function setName($name): University {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return University
     */
    public function setCountry($country) {
        $this->country = $country;
    }

    /**
     * Set the list of the domains.
     *
     * @param \string[] $domains
     * @return University
     */
    public function setDomains($domains): University {
        $this->domains = $domains;

        return $this;
    }

    /**
     * Get the list of domains associated to the university.
     *
     * @return \string[]
     */
    public function getDomains() {
        return $this->domains;
    }

    /**
     * Set the list of the pages.
     *
     * @param \string[] $pages
     * @return University
     */
    public function setPages($pages): University {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get the list of pages associated to the university.
     *
     * @return \string[]
     */
    public function getPages() {
        return $this->pages;
    }

    public function __toString(): string {
        return $this->getName();
    }

    public function getDomainsNumber(): int {
        return count($this->domains);
    }

    public function getPagesNumber(): int {
        return count($this->pages);
    }

    function getToken() {
        return $this->token;
    }

    function getState_province() {
        return $this->state_province;
    }

    function setToken($token) {
        $this->token = $token;
    }

    function setState_province($state_province) {
        $this->state_province = $state_province;
    }

}
