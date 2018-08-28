<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ApiResource
 */
class AppSettings {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $mail_host;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $mail_port;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $mail_username;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $mail_password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mail_encryption;

    public function getId() {
        return $this->id;
    }

    public function getMailHost() {
        return $this->mail_host;
    }

    public function getMailPort() {
        return $this->mail_port;
    }

    public function getMailUsername() {
        return $this->mail_username;
    }

    public function getMailPassword() {
        return $this->mail_password;
    }

    public function getMailEncryption() {
        return $this->mail_encryption;
    }

    public function setMailHost($mail_host) {
        $this->mail_host = $mail_host;
    }

    public function setMailPort($mail_port) {
        $this->mail_port = $mail_port;
    }

    public function setMailUsername($mail_username) {
        $this->mail_username = $mail_username;
    }

    public function setMailPassword($mail_password) {
        $this->mail_password = $mail_password;
    }

    public function setMailEncryption($mail_encryption) {
        $this->mail_encryption = $mail_encryption;
    }

}
