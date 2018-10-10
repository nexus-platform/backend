<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaRoles
 *
 * @ORM\Table(name="ea_roles")
 * @ORM\Entity
 */
class EaRoles
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
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=256, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=256, nullable=true)
     */
    private $slug;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_admin", type="boolean", nullable=true)
     */
    private $isAdmin;

    /**
     * @var int|null
     *
     * @ORM\Column(name="appointments", type="integer", nullable=true)
     */
    private $appointments;

    /**
     * @var int|null
     *
     * @ORM\Column(name="customers", type="integer", nullable=true)
     */
    private $customers;

    /**
     * @var int|null
     *
     * @ORM\Column(name="services", type="integer", nullable=true)
     */
    private $services;

    /**
     * @var int|null
     *
     * @ORM\Column(name="users", type="integer", nullable=true)
     */
    private $users;

    /**
     * @var int|null
     *
     * @ORM\Column(name="system_settings", type="integer", nullable=true)
     */
    private $systemSettings;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_settings", type="integer", nullable=true)
     */
    private $userSettings;


}
