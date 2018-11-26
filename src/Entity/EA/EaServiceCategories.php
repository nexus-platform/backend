<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaServiceCategories
 *
 * @ORM\Table(name="ea_service_categories", indexes={@ORM\Index(name="service_categs_assessment_center", columns={"id_assessment_center"})})
 * @ORM\Entity
 */
class EaServiceCategories
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
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \AssessmentCenter
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentCenter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_assessment_center", referencedColumnName="id")
     * })
     */
    private $idAssessmentCenter;


}
