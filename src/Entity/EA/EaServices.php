<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaServices
 *
 * @ORM\Table(name="ea_services", indexes={@ORM\Index(name="id_service_categories", columns={"id_service_categories"}), @ORM\Index(name="services_assessment_center", columns={"id_assessment_center"})})
 * @ORM\Entity
 */
class EaServices
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
     * @var int|null
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="currency", type="string", length=32, nullable=true)
     */
    private $currency;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="availabilities_type", type="string", length=32, nullable=true, options={"default"="flexible"})
     */
    private $availabilitiesType = 'flexible';

    /**
     * @var int|null
     *
     * @ORM\Column(name="attendants_number", type="integer", nullable=true, options={"default"="1"})
     */
    private $attendantsNumber = '1';

    /**
     * @var \AssessmentCenter
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentCenter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_assessment_center", referencedColumnName="id")
     * })
     */
    private $idAssessmentCenter;

    /**
     * @var \EaServiceCategories
     *
     * @ORM\ManyToOne(targetEntity="EaServiceCategories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_service_categories", referencedColumnName="id")
     * })
     */
    private $idServiceCategories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="idServices")
     */
    private $idUsers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
