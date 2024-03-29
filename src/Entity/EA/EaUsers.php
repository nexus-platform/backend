<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaUsers
 *
 * @ORM\Table(name="ea_users", indexes={@ORM\Index(name="id_roles", columns={"id_roles"}), @ORM\Index(name="user_assessment_center", columns={"id_assessment_center"})})
 * @ORM\Entity(repositoryClass="App\Repository\EA\EaUsersRepository")
 */
class EaUsers {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=256, nullable=true)
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=512, nullable=true)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=512, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mobile_number", type="string", length=128, nullable=true)
     */
    private $mobileNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone_number", type="string", length=128, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=256, nullable=true)
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=256, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="string", length=128, nullable=true)
     */
    private $state;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip_code", type="string", length=64, nullable=true)
     */
    private $zipCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", length=65535, nullable=true)
     */
    private $notes;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default"="1"})
     */
    private $status = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="id_assessment_center", type="integer", nullable=false)
     */
    private $id_assessment_center;

    /**
     * @var int
     *
     * @ORM\Column(name="id_roles", type="integer", nullable=false)
     */
    private $id_roles;

    function getId_roles() {
        return $this->id_roles;
    }

    function setId_roles($id_roles) {
        $this->id_roles = $id_roles;
    }

    function getId_assessment_center() {
        return $this->id_assessment_center;
    }

    function setId_assessment_center($id_assessment_center) {
        $this->id_assessment_center = $id_assessment_center;
    }

    function getId() {
        return $this->id;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getEmail() {
        return $this->email;
    }

    function getMobileNumber() {
        return $this->mobileNumber;
    }

    function getPhoneNumber() {
        return $this->phoneNumber;
    }

    function getAddress() {
        return $this->address;
    }

    function getCity() {
        return $this->city;
    }

    function getState() {
        return $this->state;
    }

    function getZipCode() {
        return $this->zipCode;
    }

    function getNotes() {
        return $this->notes;
    }

    function getStatus() {
        return $this->status;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setMobileNumber($mobileNumber) {
        $this->mobileNumber = $mobileNumber;
    }

    function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function setCity($city) {
        $this->city = $city;
    }

    function setState($state) {
        $this->state = $state;
    }

    function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
    }

    function setNotes($notes) {
        $this->notes = $notes;
    }

    function setStatus($status) {
        $this->status = $status;
    }
}
