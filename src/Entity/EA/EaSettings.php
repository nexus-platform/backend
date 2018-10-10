<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaSettings
 *
 * @ORM\Table(name="ea_settings", indexes={@ORM\Index(name="settings_assessment_center", columns={"id_assessment_center"})})
 * @ORM\Entity
 */
class EaSettings
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
     * @ORM\Column(name="name", type="string", length=512, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="text", length=0, nullable=true)
     */
    private $value;

    /**
     * @var \AssessmentCenter
     *
     * @ORM\ManyToOne(targetEntity="AssessmentCenter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_assessment_center", referencedColumnName="id")
     * })
     */
    private $idAssessmentCenter;


}
