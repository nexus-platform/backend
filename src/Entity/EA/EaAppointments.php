<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaAppointments
 *
 * @ORM\Table(name="ea_appointments", indexes={@ORM\Index(name="id_users_customer", columns={"id_users_customer"}), @ORM\Index(name="id_services", columns={"id_services"}), @ORM\Index(name="id_users_provider", columns={"id_users_provider"})})
 * @ORM\Entity
 */
class EaAppointments
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="book_datetime", type="datetime", nullable=true)
     */
    private $bookDatetime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="start_datetime", type="datetime", nullable=true)
     */
    private $startDatetime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="end_datetime", type="datetime", nullable=true)
     */
    private $endDatetime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", length=65535, nullable=true)
     */
    private $notes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="hash", type="text", length=65535, nullable=true)
     */
    private $hash;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_unavailable", type="boolean", nullable=true)
     */
    private $isUnavailable = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="id_google_calendar", type="text", length=65535, nullable=true)
     */
    private $idGoogleCalendar;

    /**
     * @var \EaServices
     *
     * @ORM\ManyToOne(targetEntity="EaServices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_services", referencedColumnName="id")
     * })
     */
    private $idServices;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_users_customer", referencedColumnName="id")
     * })
     */
    private $idUsersCustomer;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_users_provider", referencedColumnName="id")
     * })
     */
    private $idUsersProvider;


}
