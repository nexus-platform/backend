<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentCenterUserRepository")
 * @UniqueEntity(fields={"ac", "user"}, message="The combination ac/user should be unique")
 * @ApiResource
 */
class AssessmentCenterUser {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AssessmentCenter", inversedBy="assessment_center_users")
     * @ORM\JoinColumn(name="ac_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $ac;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assessment_center_users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="integer")
     */
    private $is_admin;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $status;

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getAc() {
        return $this->ac;
    }

    function getUser() {
        return $this->user;
    }

    function getIs_admin() {
        return $this->is_admin;
    }

    function setAc($ac) {
        $this->ac = $ac;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setIs_admin($is_admin) {
        $this->is_admin = $is_admin;
    }

    function getId() {
        return $this->id;
    }

}
