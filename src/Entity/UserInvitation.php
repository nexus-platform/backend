<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserInvitationRepository")
 * @ApiResource
 */
class UserInvitation {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $token;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $role;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var AssessmentCenter
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentCenter")
     * @ORM\JoinColumn(name="ac_id", referencedColumnName="id")
     */
    private $ac;

    function getRole() {
        return $this->role;
    }

    function setRole($role) {
        $this->role = $role;
    }

    function getAc(): AssessmentCenter {
        return $this->ac;
    }

    function setAc(AssessmentCenter $ac) {
        $this->ac = $ac;
    }

    function getToken() {
        return $this->token;
    }

    function setToken($token) {
        $this->token = $token;
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function getText() {
        return $this->text;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setText($text) {
        $this->text = $text;
    }

    function getUser(): User {
        return $this->user;
    }

    function setUser(User $user) {
        $this->user = $user;
    }

}
