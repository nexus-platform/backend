<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaConsents
 *
 * @ORM\Table(name="ea_consents", indexes={@ORM\Index(name="consents_assessment_center", columns={"id_assessment_center"})})
 * @ORM\Entity
 */
class EaConsents
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
     * @ORM\Column(name="created", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $created = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $modified = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=256, nullable=true)
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=256, nullable=true)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=512, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", length=256, nullable=true)
     */
    private $ip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=256, nullable=true)
     */
    private $type;

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
