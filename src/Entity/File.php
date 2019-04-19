<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 */
class File {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $original_filename;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $new_filename;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentCenter", inversedBy="assessment_center_files")
     * @ORM\JoinColumn(name="ac_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $ac;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_files")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $user;

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getDescription() {
        return $this->description;
    }

    function getAc() {
        return $this->ac;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function getUser() {
        return $this->user;
    }

    function getOriginal_filename() {
        return $this->original_filename;
    }

    function getNew_filename() {
        return $this->new_filename;
    }

    function setOriginal_filename($original_filename) {
        $this->original_filename = $original_filename;
    }

    function setNew_filename($new_filename) {
        $this->new_filename = $new_filename;
    }

    function setAc($ac) {
        $this->ac = $ac;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function getDate() {
        return $this->date;
    }

    function setDate($date) {
        $this->date = $date;
    }

}
