<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EaAppointmentRepository")
 * @ORM\Table(indexes={@ORM\Index(name="idx_appointment_provider", columns={"provider_id"})})
 * @ORM\Table(indexes={@ORM\Index(name="idx_appointment_student", columns={"student_id"})})
 * @ORM\Table(indexes={@ORM\Index(name="idx_appointment_service", columns={"service_id"})})
 * @ApiResource
 */
class EaAppointment {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $book_datetime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start_datetime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end_datetime;

    /**
     * @ORM\Column(type="string")
     */
    private $notes;

    /**
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @ORM\Column(type="integer")
     */
    private $is_unavailable;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    private $provider;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student;

    /**
     * @var AssessmentCenterService
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentCenterService")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;

    /**
     * @ORM\Column(type="string")
     */
    private $google_calendar_id;

    function getId() {
        return $this->id;
    }

    function getBook_datetime() {
        return $this->book_datetime;
    }

    function getStart_datetime() {
        return $this->start_datetime;
    }

    function getEnd_datetime() {
        return $this->end_datetime;
    }

    function getNotes() {
        return $this->notes;
    }

    function getHash() {
        return $this->hash;
    }

    function getIs_unavailable() {
        return $this->is_unavailable;
    }

    function getProvider(): User {
        return $this->provider;
    }

    function getStudent(): User {
        return $this->student;
    }

    function getService(): AssessmentCenterService {
        return $this->service;
    }

    function getGoogle_calendar_id() {
        return $this->google_calendar_id;
    }

    function setBook_datetime($book_datetime) {
        $this->book_datetime = $book_datetime;
    }

    function setStart_datetime($start_datetime) {
        $this->start_datetime = $start_datetime;
    }

    function setEnd_datetime($end_datetime) {
        $this->end_datetime = $end_datetime;
    }

    function setNotes($notes) {
        $this->notes = $notes;
    }

    function setHash($hash) {
        $this->hash = $hash;
    }

    function setIs_unavailable($is_unavailable) {
        $this->is_unavailable = $is_unavailable;
    }

    function setProvider(User $provider) {
        $this->provider = $provider;
    }

    function setStudent(User $student) {
        $this->student = $student;
    }

    function setService(AssessmentCenterService $service) {
        $this->service = $service;
    }

    function setGoogle_calendar_id($google_calendar_id) {
        $this->google_calendar_id = $google_calendar_id;
    }

}
