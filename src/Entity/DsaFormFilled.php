<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DsaFormFilledRepository")
 * @ApiResource
 */
class DsaFormFilled {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var DsaForm
     * @ORM\ManyToOne(targetEntity="App\Entity\DsaForm")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $dsaForm;

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
    private $signatures = [];

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $comments = [];

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $filename;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $status;

    function getSignatures() {
        return $this->signatures;
    }

    function setSignatures($signatures) {
        $this->signatures = $signatures;
    }

    function getFilename() {
        return $this->filename;
    }

    function setFilename($filename) {
        $this->filename = $filename;
    }

    function getComments() {
        return $this->comments;
    }

    function setComments($comments) {
        $this->comments = $comments;
    }

    function addComment($comment, $index) {
        $this->comments[$index][] = $comment;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
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

    function getUser(): User {
        return $this->user;
    }

    function getDsaForm(): DsaForm {
        return $this->dsaForm;
    }

    function setUser(User $user) {
        $this->user = $user;
    }

    function setDsaForm(DsaForm $dsaForm) {
        $this->dsaForm = $dsaForm;
    }

}
