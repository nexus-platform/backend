<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="Email is already in use")
 * @UniqueEntity(fields={"url"})
 */
class User implements UserInterface, Serializable {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastname;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $pre_register = [];

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 1})
     */
    private $status;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $postcode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $signature;

    /**
     *
     * @ORM\ManyToOne(targetEntity="University", cascade={"persist"})
     * @ORM\JoinColumn(name="university_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @Assert\NotBlank
     */
    private $university;

    /**
     * @ORM\OneToMany(targetEntity="AssessmentCenterUser", mappedBy="user")
     * */
    private $assessment_center_users;

    /**
     * @ORM\OneToMany(targetEntity="AssessmentCenterServiceAssessor", mappedBy="assessor")
     * */
    private $assessment_center_services_assessors;

    /**
     * @ORM\OneToMany(targetEntity="EaAppointment", mappedBy="provider")
     * */
    private $provider_appointments;

    /**
     * @ORM\OneToMany(targetEntity="EaAppointment", mappedBy="student")
     * */
    private $student_appointments;

    function getProvider_appointments() {
        return $this->provider_appointments;
    }

    function getStudent_appointments() {
        return $this->student_appointments;
    }

    function setProvider_appointments($provider_appointments) {
        $this->provider_appointments = $provider_appointments;
    }

    function setStudent_appointments($student_appointments) {
        $this->student_appointments = $student_appointments;
    }

    function getAssessment_center_services_assessors() {
        return $this->assessment_center_services_assessors;
    }

    function setAssessment_center_services_assessors($assessment_center_services_assessors) {
        $this->assessment_center_services_assessors = $assessment_center_services_assessors;
    }

    function getAssessmentCentres($returnType = 'full') {
        $acUsers = $this->getAssessment_center_users();
        $res = [];
        foreach ($acUsers as $acUser) {
            $ac = $acUser->getAc();
            switch ($returnType) {
                case 'slug':
                    $res[] = ['slug' => $ac->getUrl(), 'name' => $ac->getName()];
                    break;
                default:
                    $res[] = $ac;
                    break;
            }
        }
        return $res;
    }

    function hasRegisteredWith($ac) {
        $acUsers = $this->getAssessment_center_users();
        foreach ($acUsers as $acUser) {
            if ($acUser->getAc() === $ac) {
                return true;
            }
        }
        return false;
    }

    function getAssessment_center_users() {
        return $this->assessment_center_users;
    }

    function setAssessment_center_users($assessment_center_users) {
        $this->assessment_center_users = $assessment_center_users;
    }

    function getPre_register() {
        return $this->pre_register;
    }

    function setPre_register($pre_register) {
        $this->pre_register = $pre_register;
    }

    function getPostcode() {
        return $this->postcode;
    }

    function setPostcode($postcode) {
        $this->postcode = $postcode;
    }

    function getSignature() {
        return $this->signature;
    }

    function setSignature($signature) {
        $this->signature = $signature;
    }

    function getUniversity() {
        return $this->university;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function setUniversity($university) {
        $this->university = $university;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function getToken() {
        return $this->token;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function __toString() {
        return $this->getName() . ' ' . $this->getLastname();
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
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
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password
        ));
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized) {
        list(
                $this->id,
                $this->email,
                $this->password,
                ) = unserialize($serialized);
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles() {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void {
        $this->roles = $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername() {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {
        // TODO: Implement eraseCredentials() method.
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
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function isStudent() {
        return in_array('student', $this->getRoles());
    }

    public function isDO() {
        return in_array('do', $this->getRoles());
    }

    public function isNA() {
        return in_array('na', $this->getRoles());
    }

    public function getFullname() {
        return $this->name . ' ' . $this->lastname;
    }

}
