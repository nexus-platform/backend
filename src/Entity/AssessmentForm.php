<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssessmentFormRepository")
 * @ApiResource
 */
class AssessmentForm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentCenter", inversedBy="forms")
     */
    private $center;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $publishedAt;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $user;

    //--- Form data ---//

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $sex;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_of_birth;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $home_address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $term_address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $previously_assessed;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dsa_assessed_email;

    /**
     * @var UploadedFile
     * @ORM\Column(type="string", nullable=true)
     */
    private $dsa_assessed_attachement;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $student_finance_england;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $sfw;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $sfni;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $saas;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nhs;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dsa_eligibility_letter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $customer_reference_number;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $course_title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $course_type;

    //Graduate type
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $select_type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $year_of_study;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $course_date_start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $course_date_end;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $learning_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $learning_address;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $disability_team_contact;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $disability_team_tel;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $disability_team_email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $course_leader_contact;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $course_leader_tel;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $course_leader_email;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $permission_share;
    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $permission;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type_disability;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $main_difficulties;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type_of_support;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type_of_equipment;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $special_access_requirements;

    // Documents
    /**
     * @var UploadedFile
     * @ORM\Column(type="string", nullable=true)
     */
    private $dsa_eligibility_letter_current;

    /**
     * @var UploadedFile
     * @ORM\Column(type="string", nullable=true)
     */
    private $diagnostic_assessment_documents;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $assurance_and_training;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $given_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $last_name_req;

    public function __toString()
    {
        return 'Applications #' . $this->getId();
    }

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
        $this->status = false;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth() : ?\DateTime
    {
        return $this->date_of_birth;
    }

    /**
     * @param \DateTime $date_of_birth
     */
    public function setDateOfBirth(\DateTime $date_of_birth)
    {
        $this->date_of_birth = $date_of_birth;
    }

    /**
     * @return mixed
     */
    public function getHomeAddress()
    {
        return $this->home_address;
    }

    /**
     * @param mixed $home_address
     */
    public function setHomeAddress($home_address)
    {
        $this->home_address = $home_address;
    }

    /**
     * @return mixed
     */
    public function getTermAddress()
    {
        return $this->term_address;
    }

    /**
     * @param mixed $term_address
     */
    public function setTermAddress($term_address)
    {
        $this->term_address = $term_address;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPreviouslyAssessed()
    {
        return $this->previously_assessed;
    }

    /**
     * @param mixed $previously_assessed
     */
    public function setPreviouslyAssessed($previously_assessed)
    {
        $this->previously_assessed = $previously_assessed;
    }

    /**
     * @return \DateTime
     */
    public function getDate() : ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDsaAssessedEmail()
    {
        return $this->dsa_assessed_email;
    }

    /**
     * @param mixed $dsa_assessed_email
     */
    public function setDsaAssessedEmail($dsa_assessed_email)
    {
        $this->dsa_assessed_email = $dsa_assessed_email;
    }

    /**
     * @return mixed
     */
    public function getDsaAssessedAttachement()
    {
        return $this->dsa_assessed_attachement;
    }

    /**
     * @param mixed $dsa_assessed_attachement
     */
    public function setDsaAssessedAttachement($dsa_assessed_attachement)
    {
        $this->dsa_assessed_attachement = $dsa_assessed_attachement;
    }

    /**
     * @return mixed
     */
    public function getStudentFinanceEngland()
    {
        return $this->student_finance_england;
    }

    /**
     * @param mixed $student_finance_england
     */
    public function setStudentFinanceEngland($student_finance_england)
    {
        $this->student_finance_england = $student_finance_england;
    }

    /**
     * @return mixed
     */
    public function getSfw()
    {
        return $this->sfw;
    }

    /**
     * @param mixed $sfw
     */
    public function setSfw($sfw)
    {
        $this->sfw = $sfw;
    }

    /**
     * @return mixed
     */
    public function getSfni()
    {
        return $this->sfni;
    }

    /**
     * @param mixed $sfni
     */
    public function setSfni($sfni)
    {
        $this->sfni = $sfni;
    }

    /**
     * @return mixed
     */
    public function getSaas()
    {
        return $this->saas;
    }

    /**
     * @param mixed $saas
     */
    public function setSaas($saas)
    {
        $this->saas = $saas;
    }

    /**
     * @return mixed
     */
    public function getNhs()
    {
        return $this->nhs;
    }

    /**
     * @param mixed $nhs
     */
    public function setNhs($nhs)
    {
        $this->nhs = $nhs;
    }

    /**
     * @return mixed
     */
    public function getDsaEligibilityLetter()
    {
        return $this->dsa_eligibility_letter;
    }

    /**
     * @param mixed $dsa_eligibility_letter
     */
    public function setDsaEligibilityLetter($dsa_eligibility_letter)
    {
        $this->dsa_eligibility_letter = $dsa_eligibility_letter;
    }

    /**
     * @return mixed
     */
    public function getCustomerReferenceNumber()
    {
        return $this->customer_reference_number;
    }

    /**
     * @param mixed $customer_reference_number
     */
    public function setCustomerReferenceNumber($customer_reference_number)
    {
        $this->customer_reference_number = $customer_reference_number;
    }

    /**
     * @return mixed
     */
    public function getCourseTitle()
    {
        return $this->course_title;
    }

    /**
     * @param mixed $course_title
     */
    public function setCourseTitle($course_title)
    {
        $this->course_title = $course_title;
    }

    /**
     * @return mixed
     */
    public function getCourseType()
    {
        return $this->course_type;
    }

    /**
     * @param mixed $course_type
     */
    public function setCourseType($course_type)
    {
        $this->course_type = $course_type;
    }

    /**
     * @return mixed
     */
    public function getSelectType()
    {
        return $this->select_type;
    }

    /**
     * @param mixed $select_type
     */
    public function setSelectType($select_type)
    {
        $this->select_type = $select_type;
    }

    /**
     * @return mixed
     */
    public function getYearOfStudy()
    {
        return $this->year_of_study;
    }

    /**
     * @param mixed $year_of_study
     */
    public function setYearOfStudy($year_of_study)
    {
        $this->year_of_study = $year_of_study;
    }

    /**
     * @return \DateTime
     */
    public function getCourseDateStart(): ?\DateTime
    {
        return $this->course_date_start;
    }

    /**
     * @param \DateTime $course_date_start
     */
    public function setCourseDateStart(\DateTime $course_date_start)
    {
        $this->course_date_start = $course_date_start;
    }

    /**
     * @return \DateTime
     */
    public function getCourseDateEnd(): ?\DateTime
    {
        return $this->course_date_end;
    }

    /**
     * @param \DateTime $course_date_end
     */
    public function setCourseDateEnd(\DateTime $course_date_end)
    {
        $this->course_date_end = $course_date_end;
    }

    /**
     * @return mixed
     */
    public function getLearningName()
    {
        return $this->learning_name;
    }

    /**
     * @param mixed $learning_name
     */
    public function setLearningName($learning_name)
    {
        $this->learning_name = $learning_name;
    }

    /**
     * @return mixed
     */
    public function getLearningAddress()
    {
        return $this->learning_address;
    }

    /**
     * @param mixed $learning_address
     */
    public function setLearningAddress($learning_address)
    {
        $this->learning_address = $learning_address;
    }

    /**
     * @return mixed
     */
    public function getDisabilityTeamContact()
    {
        return $this->disability_team_contact;
    }

    /**
     * @param mixed $disability_team_contact
     */
    public function setDisabilityTeamContact($disability_team_contact)
    {
        $this->disability_team_contact = $disability_team_contact;
    }

    /**
     * @return mixed
     */
    public function getDisabilityTeamTel()
    {
        return $this->disability_team_tel;
    }

    /**
     * @param mixed $disability_team_tel
     */
    public function setDisabilityTeamTel($disability_team_tel)
    {
        $this->disability_team_tel = $disability_team_tel;
    }

    /**
     * @return mixed
     */
    public function getDisabilityTeamEmail()
    {
        return $this->disability_team_email;
    }

    /**
     * @param mixed $disability_team_email
     */
    public function setDisabilityTeamEmail($disability_team_email)
    {
        $this->disability_team_email = $disability_team_email;
    }

    /**
     * @return mixed
     */
    public function getCourseLeaderContact()
    {
        return $this->course_leader_contact;
    }

    /**
     * @param mixed $course_leader_contact
     */
    public function setCourseLeaderContact($course_leader_contact)
    {
        $this->course_leader_contact = $course_leader_contact;
    }

    /**
     * @return mixed
     */
    public function getCourseLeaderTel()
    {
        return $this->course_leader_tel;
    }

    /**
     * @param mixed $course_leader_tel
     */
    public function setCourseLeaderTel($course_leader_tel)
    {
        $this->course_leader_tel = $course_leader_tel;
    }

    /**
     * @return mixed
     */
    public function getCourseLeaderEmail()
    {
        return $this->course_leader_email;
    }

    /**
     * @param mixed $course_leader_email
     */
    public function setCourseLeaderEmail($course_leader_email)
    {
        $this->course_leader_email = $course_leader_email;
    }

    /**
     * @return mixed
     */
    public function getPermissionShare()
    {
        return $this->permission_share;
    }

    /**
     * @param mixed $permission_share
     */
    public function setPermissionShare($permission_share)
    {
        $this->permission_share = $permission_share;
    }

    /**
     * @return mixed
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param mixed $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return mixed
     */
    public function getTypeDisability()
    {
        return $this->type_disability;
    }

    /**
     * @param mixed $type_disability
     */
    public function setTypeDisability($type_disability)
    {
        $this->type_disability = $type_disability;
    }

    /**
     * @return mixed
     */
    public function getMainDifficulties()
    {
        return $this->main_difficulties;
    }

    /**
     * @param mixed $main_difficulties
     */
    public function setMainDifficulties($main_difficulties)
    {
        $this->main_difficulties = $main_difficulties;
    }

    /**
     * @return mixed
     */
    public function getTypeOfSupport()
    {
        return $this->type_of_support;
    }

    /**
     * @param mixed $type_of_support
     */
    public function setTypeOfSupport($type_of_support)
    {
        $this->type_of_support = $type_of_support;
    }

    /**
     * @return mixed
     */
    public function getTypeOfEquipment()
    {
        return $this->type_of_equipment;
    }

    /**
     * @param mixed $type_of_equipment
     */
    public function setTypeOfEquipment($type_of_equipment)
    {
        $this->type_of_equipment = $type_of_equipment;
    }

    /**
     * @return mixed
     */
    public function getSpecialAccessRequirements()
    {
        return $this->special_access_requirements;
    }

    /**
     * @param mixed $special_access_requirements
     */
    public function setSpecialAccessRequirements($special_access_requirements)
    {
        $this->special_access_requirements = $special_access_requirements;
    }

    /**
     * @return mixed
     */
    public function getDsaEligibilityLetterCurrent()
    {
        return $this->dsa_eligibility_letter_current;
    }

    /**
     * @param mixed $dsa_eligibility_letter_current
     */
    public function setDsaEligibilityLetterCurrent($dsa_eligibility_letter_current)
    {
        $this->dsa_eligibility_letter_current = $dsa_eligibility_letter_current;
    }

    /**
     * @return mixed
     */
    public function getDiagnosticAssessmentDocuments()
    {
        return $this->diagnostic_assessment_documents;
    }

    /**
     * @param mixed $diagnostic_assessment_documents
     */
    public function setDiagnosticAssessmentDocuments($diagnostic_assessment_documents)
    {
        $this->diagnostic_assessment_documents = $diagnostic_assessment_documents;
    }

    /**
     * @return mixed
     */
    public function getAssuranceAndTraining()
    {
        return $this->assurance_and_training;
    }

    /**
     * @param mixed $assurance_and_training
     */
    public function setAssuranceAndTraining($assurance_and_training)
    {
        $this->assurance_and_training = $assurance_and_training;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->given_name;
    }

    /**
     * @param mixed $given_name
     */
    public function setGivenName($given_name)
    {
        $this->given_name = $given_name;
    }

    /**
     * @return mixed
     */
    public function getLastNameReq()
    {
        return $this->last_name_req;
    }

    /**
     * @param mixed $last_name_req
     */
    public function setLastNameReq($last_name_req)
    {
        $this->last_name_req = $last_name_req;
    }

    /**
     * @return mixed
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * @param mixed $center
     */
    public function setCenter($center)
    {
        $this->center = $center;
    }

    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
