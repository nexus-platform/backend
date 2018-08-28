<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\Table(indexes={@ORM\Index(name="idx_notif_type", columns={"type"})})
 * @ApiResource
 */
class Notification {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $subtitle;

    /**
     * @ORM\Column(type="string")
     */
    private $headline;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 1})
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 1})
     */
    private $type;

    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
    }

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    public function getId() {
        return $this->id;
    }

    function getUser(): User {
        return $this->user;
    }

    function setUser(User $user) {
        $this->user = $user;
    }

    function getTitle() {
        return $this->title;
    }

    function getSubtitle() {
        return $this->subtitle;
    }

    function getHeadline() {
        return $this->headline;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setSubtitle($subtitle) {
        $this->subtitle = $subtitle;
    }

    function setHeadline($headline) {
        $this->headline = $headline;
    }

}
