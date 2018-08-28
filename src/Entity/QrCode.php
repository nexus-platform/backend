<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QrCodeRepository")
 * @ApiResource
 */
class QrCode {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $random_code;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $created_at;

    function getId() {
        return $this->id;
    }

    function getContent() {
        return $this->content;
    }

    function getRandom_code() {
        return $this->random_code;
    }

    function setContent($content) {
        $this->content = $content;
    }

    function setRandom_code($random_code) {
        $this->random_code = $random_code;
    }

    function getUser(): User {
        return $this->user;
    }

    function setUser(User $user) {
        $this->user = $user;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

}
