<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisabilityOfficerRepository")
 * @ApiResource
 */
class DisabilityOfficer {

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
     * Custom URL
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $admin;

    /**
     * @var DsaForm[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\DsaForm", cascade={"persist"})
     * @ORM\JoinTable(name="officer_form")
     */
    private $forms;

    /**
     * @var DsaSlim[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\DsaSlim",
     *     mappedBy="center",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $dsa_slims;

    public function __construct() {
        $forms = new ArrayCollection();
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
    public function getId() {
        return $this->id;
    }

    public function __toString() {
        return $this->getName();
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

    public function addForm(DsaForm ...$tags): void {
        foreach ($tags as $tag) {
            if (!$this->forms->contains($tag)) {
                $this->forms->add($tag);
            }
        }
    }

    public function removeForm(DsaForm $tag): void {
        $this->forms->removeElement($tag);
    }

    /**
     * @return DsaForm[]|ArrayCollection
     */
    public function getForms() {
        return $this->forms;
    }

    /**
     * @return DsaSlim[]|ArrayCollection
     */
    public function getDsaSlims() {
        return $this->dsa_slims;
    }

    public function addDsaSlim(DsaSlim $form): void {
        $form->setCenter($this);
        if (!$this->dsa_slims->contains($form)) {
            $this->dsa_slims->add($form);
        }
    }

    public function removeDsaSlim(DsaSlim $form): void {
        $form->setCenter(null);
        $this->dsa_slims->removeElement($form);
    }

}
