<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LabelRepository")
 */
class Label implements \JsonSerializable
{
    public function __construct()
    {
        $this->name = 'Name';
        $this->show_name = true;
        $this->last_name = 'Last Name';
        $this->show_last_name = true;
        $this->sex = 'Sex';
        $this->show_sex = true;
        $this->date_of_birth = 'Date of Birth';
        $this->show_date_of_birth = true;
        $this->home_address = 'Home Address';
        $this->show_home_address = true;
        $this->term_address = 'Term Address';
        $this->show_term_address = true;
        $this->phone = 'Phone';
        $this->show_phone = true;
        $this->mobile = 'Mobile';
        $this->show_mobile = true;
        $this->email = 'Email';
        $this->show_email = true;

        $this->previously_assessed = 'Have you been previously assessed for DSA funding?';
        $this->show_previously_assessed = true;
        $this->date = 'Date';
        $this->show_date = true;
        $this->dsa_assessed_email = 'Email';
        $this->show_dsa_assessed_email = true;
        $this->dsa_assessed_attachement = 'DSA Assessed Attachement';
        $this->show_dsa_assessed_attachement = true;

        $this->student_finance_england = 'Student Finance England';
        $this->show_student_finance_england = true;
        $this->sfw = 'SFW';
        $this->show_sfw = true;
        $this->sfni = 'SFNI';
        $this->show_sfni = true;
        $this->saas = 'SAAS';
        $this->show_saas = true;
        $this->nhs = 'NHS';
        $this->show_nhs = true;

        $this->dsa_eligibility_letter = 'Have you received a DSA eligibility letter from your Funding Body?';
        $this->show_dsa_eligibility_letter = true;
        $this->customer_reference_number = 'Customer Reference Number (CRN)';
        $this->show_customer_reference_number = true;

        $this->course_title = 'Course Title';
        $this->show_course_title = true;
        $this->course_type = 'Course type';
        $this->show_course_type = true;

        $this->select_type = 'Select type';
        $this->show_select_type = true;
        $this->year_of_study = 'Year of Study (e.g. 1/3)';
        $this->show_year_of_study = true;

        $this->course_dates = 'Course Dates';
        $this->show_course_dates = true;
        $this->learning_name = 'University/Institution of Learning Name';
        $this->show_learning_name = true;
        $this->learning_address = 'University/Institution of Learning Address';
        $this->show_learning_address = true;

        $this->disability_team_contact = 'Disability Team Contact (if known)';
        $this->show_disability_team_contact = true;
        $this->disability_team_tel = 'Disability Team Tel';
        $this->show_disability_team_tel = true;
        $this->disability_team_email = 'Disability Team Email';
        $this->show_disability_team_email = true;

        $this->course_leader_contact = 'Course Leader Contact (if known)';
        $this->show_course_leader_contact = true;
        $this->course_leader_tel = 'Course Leader Tel';
        $this->show_course_leader_tel = true;
        $this->course_leader_email = 'Course Leader Email';
        $this->show_course_leader_email = true;

        $this->permission_share = 'Do you give us permission to share your medical/diagnostic records with your Assessor?';
        $this->show_permission_share = true;
        $this->permission = 'We will not disclose your identity to your university / college without your permission. However, it may be helpful for us to contact your disability officer / course leader for information regarding your course. Please confirm if you are happy to give your permission';
        $this->show_permission = true;
        $this->type_disability = 'What type of disability are you being assessed for (you will find this in your DSA eligibility letter)?';
        $this->show_type_disability = true;
        $this->main_difficulties = 'What are the main difficulties caused by your disability?';
        $this->show_main_difficulties = true;
        $this->type_of_support = 'What type of support have you received in the past (e.g. in school / college)?e.g. did you get extra time for exams, or did you get 1:1 support, etc.';
        $this->show_type_of_support = true;
        $this->type_of_equipment = 'What type of equipment do you have access to (e.g. computer, tablet, smartphone). Please provide details of the make and model of each, and do feel free to bring along any such piece of equipment you use to your assessment.';
        $this->show_type_of_equipment = true;
        $this->special_access_requirements = 'Do you have special access requirements for your assessment? E.g. BSL interpreter, parking, etc.';
        $this->show_special_access_requirements = true;

        $this->dsa_eligibility_letter_current = 'DSA Eligibility Letter';
        $this->show_dsa_eligibility_letter_current = true;
        $this->diagnostic_assessment_documents = 'Diagnostic/medical Documents';
        $this->show_diagnostic_assessment_documents = true;

        $this->assurance_and_training = 'I give my consent for another person to observe the Assessment for the purposes of quality assurance and training of personnel.';
        $this->show_assurance_and_training = true;

        $this->given_name = 'Given Name(s)';
        $this->show_given_name = true;
        $this->last_name_req = 'Last Name (required)';
        $this->show_last_name_req = true;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default"="Name"})
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=true})
     */
    private $show_name;

    /**
     * @ORM\Column(type="string")
     */
    private $last_name;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_last_name;

    /**
     * @ORM\Column(type="string")
     */
    private $sex;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_sex;

    /**
     * @ORM\Column(type="string")
     */
    private $date_of_birth;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_date_of_birth;

    /**
     * @ORM\Column(type="string")
     */
    private $home_address;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_home_address;

    /**
     * @ORM\Column(type="string")
     */
    private $term_address;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_term_address;

    /**
     * @ORM\Column(type="string")
     */
    private $phone;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_phone;

    /**
     * @ORM\Column(type="string")
     */
    private $mobile;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_mobile;

    /**
     * @ORM\Column(type="string")
     */
    private $email;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_email;

    // Have you been previously assessed for DSA funding?
    /**
     * @ORM\Column(type="string")
     */
    private $previously_assessed;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_previously_assessed;

    /**
     * @ORM\Column(type="string")
     */
    private $date;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_date;

    /**
     * @ORM\Column(type="string")
     */
    private $dsa_assessed_email;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_dsa_assessed_email;

    /**
     * @ORM\Column(type="string")
     */
    private $dsa_assessed_attachement;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_dsa_assessed_attachement;

    // Funding Body
    /**
     * @ORM\Column(type="string")
     */
    private $student_finance_england;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_student_finance_england;

    /**
     * @ORM\Column(type="string")
     */
    private $sfw;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_sfw;

    /**
     * @ORM\Column(type="string")
     */
    private $sfni;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_sfni;

    /**
     * @ORM\Column(type="string")
     */
    private $saas;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_saas;

    /**
     * @ORM\Column(type="string")
     */
    private $nhs;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_nhs;

    //DSA eligibility letter
    /**
     * @ORM\Column(type="string")
     */
    private $dsa_eligibility_letter;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_dsa_eligibility_letter;

    /**
     * @ORM\Column(type="string")
     */
    private $customer_reference_number;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_customer_reference_number;

    //Course
    /**
     * @ORM\Column(type="string")
     */
    private $course_title;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_course_title;

    /**
     * @ORM\Column(type="string")
     */
    private $course_type;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_course_type;

    //Graduate type
    /**
     * @ORM\Column(type="string")
     */
    private $select_type;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_select_type;

    /**
     * @ORM\Column(type="string")
     */
    private $year_of_study;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_year_of_study;

    // Course data
    /**
     * @ORM\Column(type="string")
     */
    private $course_dates;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_course_dates;

    /**
     * @ORM\Column(type="string")
     */
    private $learning_name;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_learning_name;

    /**
     * @ORM\Column(type="string")
     */
    private $learning_address;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_learning_address;

    //Disability Team
    /**
     * @ORM\Column(type="string")
     */
    private $disability_team_contact;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_disability_team_contact;

    /**
     * @ORM\Column(type="string")
     */
    private $disability_team_tel;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_disability_team_tel;

    /**
     * @ORM\Column(type="string")
     */
    private $disability_team_email;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_disability_team_email;

    // Course Leader
    /**
     * @ORM\Column(type="string")
     */
    private $course_leader_contact;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_course_leader_contact;

    /**
     * @ORM\Column(type="string")
     */
    private $course_leader_tel;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_course_leader_tel;

    /**
     * @ORM\Column(type="string")
     */
    private $course_leader_email;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_course_leader_email;

    // Your Documents
    /**
     * @ORM\Column(type="string", length=512)
     */
    private $permission_share;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_permission_share;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $permission;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_permission;

    /**
     * @ORM\Column(type="string")
     */
    private $type_disability;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_type_disability;

    /**
     * @ORM\Column(type="string")
     */
    private $main_difficulties;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_main_difficulties;

    /**
     * @ORM\Column(type="string")
     */
    private $type_of_support;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_type_of_support;

    /**
     * @ORM\Column(type="string")
     */
    private $type_of_equipment;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_type_of_equipment;

    /**
     * @ORM\Column(type="string")
     */
    private $special_access_requirements;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_special_access_requirements;

    // Documents
    /**
     * @ORM\Column(type="string")
     */
    private $dsa_eligibility_letter_current;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_dsa_eligibility_letter_current;

    /**
     * @ORM\Column(type="string")
     */
    private $diagnostic_assessment_documents;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_diagnostic_assessment_documents;

    /**
     * @ORM\Column(type="string")
     */
    private $assurance_and_training;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_assurance_and_training;

    /**
     * @ORM\Column(type="string")
     */
    private $given_name;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_given_name;

    /**
     * @ORM\Column(type="string")
     */
    private $last_name_req;
    /**
     * @ORM\Column(type="boolean")
     */
    private $show_last_name_req;

    /**
     * @var AssessmentCenter
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\AssessmentCenter", inversedBy="labels")
     */
    private $center;

    /**
     * @return AssessmentCenter
     */
    public function getUser(): AssessmentCenter
    {
        return $this->center;
    }

    /**
     * @param AssessmentCenter $user
     */
    public function setUser(AssessmentCenter $user)
    {
        $this->center = $user;
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
    public function getShowName()
    {
        return $this->show_name;
    }

    /**
     * @param mixed $show_name
     */
    public function setShowName($show_name)
    {
        $this->show_name = $show_name;
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
    public function getShowLastName()
    {
        return $this->show_last_name;
    }

    /**
     * @param mixed $show_last_name
     */
    public function setShowLastName($show_last_name)
    {
        $this->show_last_name = $show_last_name;
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
     * @return mixed
     */
    public function getShowSex()
    {
        return $this->show_sex;
    }

    /**
     * @param mixed $show_sex
     */
    public function setShowSex($show_sex)
    {
        $this->show_sex = $show_sex;
    }

    /**
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * @param mixed $date_of_birth
     */
    public function setDateOfBirth($date_of_birth)
    {
        $this->date_of_birth = $date_of_birth;
    }

    /**
     * @return mixed
     */
    public function getShowDateOfBirth()
    {
        return $this->show_date_of_birth;
    }

    /**
     * @param mixed $show_date_of_birth
     */
    public function setShowDateOfBirth($show_date_of_birth)
    {
        $this->show_date_of_birth = $show_date_of_birth;
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
    public function getShowHomeAddress()
    {
        return $this->show_home_address;
    }

    /**
     * @param mixed $show_home_address
     */
    public function setShowHomeAddress($show_home_address)
    {
        $this->show_home_address = $show_home_address;
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
    public function getShowTermAddress()
    {
        return $this->show_term_address;
    }

    /**
     * @param mixed $show_term_address
     */
    public function setShowTermAddress($show_term_address)
    {
        $this->show_term_address = $show_term_address;
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
    public function getShowPhone()
    {
        return $this->show_phone;
    }

    /**
     * @param mixed $show_phone
     */
    public function setShowPhone($show_phone)
    {
        $this->show_phone = $show_phone;
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
    public function getShowMobile()
    {
        return $this->show_mobile;
    }

    /**
     * @param mixed $show_mobile
     */
    public function setShowMobile($show_mobile)
    {
        $this->show_mobile = $show_mobile;
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
    public function getShowEmail()
    {
        return $this->show_email;
    }

    /**
     * @param mixed $show_email
     */
    public function setShowEmail($show_email)
    {
        $this->show_email = $show_email;
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
     * @return mixed
     */
    public function getShowPreviouslyAssessed()
    {
        return $this->show_previously_assessed;
    }

    /**
     * @param mixed $show_previously_assessed
     */
    public function setShowPreviouslyAssessed($show_previously_assessed)
    {
        $this->show_previously_assessed = $show_previously_assessed;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getShowDate()
    {
        return $this->show_date;
    }

    /**
     * @param mixed $show_date
     */
    public function setShowDate($show_date)
    {
        $this->show_date = $show_date;
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
    public function getShowDsaAssessedEmail()
    {
        return $this->show_dsa_assessed_email;
    }

    /**
     * @param mixed $show_dsa_assessed_email
     */
    public function setShowDsaAssessedEmail($show_dsa_assessed_email)
    {
        $this->show_dsa_assessed_email = $show_dsa_assessed_email;
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
    public function getShowDsaAssessedAttachement()
    {
        return $this->show_dsa_assessed_attachement;
    }

    /**
     * @param mixed $show_dsa_assessed_attachement
     */
    public function setShowDsaAssessedAttachement($show_dsa_assessed_attachement)
    {
        $this->show_dsa_assessed_attachement = $show_dsa_assessed_attachement;
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
    public function getShowStudentFinanceEngland()
    {
        return $this->show_student_finance_england;
    }

    /**
     * @param mixed $show_student_finance_england
     */
    public function setShowStudentFinanceEngland($show_student_finance_england)
    {
        $this->show_student_finance_england = $show_student_finance_england;
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
    public function getShowSfw()
    {
        return $this->show_sfw;
    }

    /**
     * @param mixed $show_sfw
     */
    public function setShowSfw($show_sfw)
    {
        $this->show_sfw = $show_sfw;
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
    public function getShowSfni()
    {
        return $this->show_sfni;
    }

    /**
     * @param mixed $show_sfni
     */
    public function setShowSfni($show_sfni)
    {
        $this->show_sfni = $show_sfni;
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
    public function getShowSaas()
    {
        return $this->show_saas;
    }

    /**
     * @param mixed $show_saas
     */
    public function setShowSaas($show_saas)
    {
        $this->show_saas = $show_saas;
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
    public function getShowNhs()
    {
        return $this->show_nhs;
    }

    /**
     * @param mixed $show_nhs
     */
    public function setShowNhs($show_nhs)
    {
        $this->show_nhs = $show_nhs;
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
    public function getShowDsaEligibilityLetter()
    {
        return $this->show_dsa_eligibility_letter;
    }

    /**
     * @param mixed $show_dsa_eligibility_letter
     */
    public function setShowDsaEligibilityLetter($show_dsa_eligibility_letter)
    {
        $this->show_dsa_eligibility_letter = $show_dsa_eligibility_letter;
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
    public function getShowCustomerReferenceNumber()
    {
        return $this->show_customer_reference_number;
    }

    /**
     * @param mixed $show_customer_reference_number
     */
    public function setShowCustomerReferenceNumber($show_customer_reference_number)
    {
        $this->show_customer_reference_number = $show_customer_reference_number;
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
    public function getShowCourseTitle()
    {
        return $this->show_course_title;
    }

    /**
     * @param mixed $show_course_title
     */
    public function setShowCourseTitle($show_course_title)
    {
        $this->show_course_title = $show_course_title;
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
    public function getShowCourseType()
    {
        return $this->show_course_type;
    }

    /**
     * @param mixed $show_course_type
     */
    public function setShowCourseType($show_course_type)
    {
        $this->show_course_type = $show_course_type;
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
    public function getShowSelectType()
    {
        return $this->show_select_type;
    }

    /**
     * @param mixed $show_select_type
     */
    public function setShowSelectType($show_select_type)
    {
        $this->show_select_type = $show_select_type;
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
     * @return mixed
     */
    public function getShowYearOfStudy()
    {
        return $this->show_year_of_study;
    }

    /**
     * @param mixed $show_year_of_study
     */
    public function setShowYearOfStudy($show_year_of_study)
    {
        $this->show_year_of_study = $show_year_of_study;
    }

    /**
     * @return mixed
     */
    public function getCourseDates()
    {
        return $this->course_dates;
    }

    /**
     * @param mixed $course_dates
     */
    public function setCourseDates($course_dates)
    {
        $this->course_dates = $course_dates;
    }

    /**
     * @return mixed
     */
    public function getShowCourseDates()
    {
        return $this->show_course_dates;
    }

    /**
     * @param mixed $show_course_dates
     */
    public function setShowCourseDates($show_course_dates)
    {
        $this->show_course_dates = $show_course_dates;
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
    public function getShowLearningName()
    {
        return $this->show_learning_name;
    }

    /**
     * @param mixed $show_learning_name
     */
    public function setShowLearningName($show_learning_name)
    {
        $this->show_learning_name = $show_learning_name;
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
    public function getShowLearningAddress()
    {
        return $this->show_learning_address;
    }

    /**
     * @param mixed $show_learning_address
     */
    public function setShowLearningAddress($show_learning_address)
    {
        $this->show_learning_address = $show_learning_address;
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
    public function getShowDisabilityTeamContact()
    {
        return $this->show_disability_team_contact;
    }

    /**
     * @param mixed $show_disability_team_contact
     */
    public function setShowDisabilityTeamContact($show_disability_team_contact)
    {
        $this->show_disability_team_contact = $show_disability_team_contact;
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
    public function getShowDisabilityTeamTel()
    {
        return $this->show_disability_team_tel;
    }

    /**
     * @param mixed $show_disability_team_tel
     */
    public function setShowDisabilityTeamTel($show_disability_team_tel)
    {
        $this->show_disability_team_tel = $show_disability_team_tel;
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
    public function getShowDisabilityTeamEmail()
    {
        return $this->show_disability_team_email;
    }

    /**
     * @param mixed $show_disability_team_email
     */
    public function setShowDisabilityTeamEmail($show_disability_team_email)
    {
        $this->show_disability_team_email = $show_disability_team_email;
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
    public function getShowCourseLeaderContact()
    {
        return $this->show_course_leader_contact;
    }

    /**
     * @param mixed $show_course_leader_contact
     */
    public function setShowCourseLeaderContact($show_course_leader_contact)
    {
        $this->show_course_leader_contact = $show_course_leader_contact;
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
    public function getShowCourseLeaderTel()
    {
        return $this->show_course_leader_tel;
    }

    /**
     * @param mixed $show_course_leader_tel
     */
    public function setShowCourseLeaderTel($show_course_leader_tel)
    {
        $this->show_course_leader_tel = $show_course_leader_tel;
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
    public function getShowCourseLeaderEmail()
    {
        return $this->show_course_leader_email;
    }

    /**
     * @param mixed $show_course_leader_email
     */
    public function setShowCourseLeaderEmail($show_course_leader_email)
    {
        $this->show_course_leader_email = $show_course_leader_email;
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
    public function getShowPermissionShare()
    {
        return $this->show_permission_share;
    }

    /**
     * @param mixed $show_permission_share
     */
    public function setShowPermissionShare($show_permission_share)
    {
        $this->show_permission_share = $show_permission_share;
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
    public function getShowPermission()
    {
        return $this->show_permission;
    }

    /**
     * @param mixed $show_permission
     */
    public function setShowPermission($show_permission)
    {
        $this->show_permission = $show_permission;
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
    public function getShowTypeDisability()
    {
        return $this->show_type_disability;
    }

    /**
     * @param mixed $show_type_disability
     */
    public function setShowTypeDisability($show_type_disability)
    {
        $this->show_type_disability = $show_type_disability;
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
    public function getShowMainDifficulties()
    {
        return $this->show_main_difficulties;
    }

    /**
     * @param mixed $show_main_difficulties
     */
    public function setShowMainDifficulties($show_main_difficulties)
    {
        $this->show_main_difficulties = $show_main_difficulties;
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
    public function getShowTypeOfSupport()
    {
        return $this->show_type_of_support;
    }

    /**
     * @param mixed $show_type_of_support
     */
    public function setShowTypeOfSupport($show_type_of_support)
    {
        $this->show_type_of_support = $show_type_of_support;
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
    public function getShowTypeOfEquipment()
    {
        return $this->show_type_of_equipment;
    }

    /**
     * @param mixed $show_type_of_equipment
     */
    public function setShowTypeOfEquipment($show_type_of_equipment)
    {
        $this->show_type_of_equipment = $show_type_of_equipment;
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
    public function getShowSpecialAccessRequirements()
    {
        return $this->show_special_access_requirements;
    }

    /**
     * @param mixed $show_special_access_requirements
     */
    public function setShowSpecialAccessRequirements($show_special_access_requirements)
    {
        $this->show_special_access_requirements = $show_special_access_requirements;
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
    public function getShowDsaEligibilityLetterCurrent()
    {
        return $this->show_dsa_eligibility_letter_current;
    }

    /**
     * @param mixed $show_dsa_eligibility_letter_current
     */
    public function setShowDsaEligibilityLetterCurrent($show_dsa_eligibility_letter_current)
    {
        $this->show_dsa_eligibility_letter_current = $show_dsa_eligibility_letter_current;
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
    public function getShowDiagnosticAssessmentDocuments()
    {
        return $this->show_diagnostic_assessment_documents;
    }

    /**
     * @param mixed $show_diagnostic_assessment_documents
     */
    public function setShowDiagnosticAssessmentDocuments($show_diagnostic_assessment_documents)
    {
        $this->show_diagnostic_assessment_documents = $show_diagnostic_assessment_documents;
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
    public function getShowAssuranceAndTraining()
    {
        return $this->show_assurance_and_training;
    }

    /**
     * @param mixed $show_assurance_and_training
     */
    public function setShowAssuranceAndTraining($show_assurance_and_training)
    {
        $this->show_assurance_and_training = $show_assurance_and_training;
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
    public function getShowGivenName()
    {
        return $this->show_given_name;
    }

    /**
     * @param mixed $show_given_name
     */
    public function setShowGivenName($show_given_name)
    {
        $this->show_given_name = $show_given_name;
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
    public function getShowLastNameReq()
    {
        return $this->show_last_name_req;
    }

    /**
     * @param mixed $show_last_name_req
     */
    public function setShowLastNameReq($show_last_name_req)
    {
        $this->show_last_name_req = $show_last_name_req;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return array(
            'name' => array(
                $this->getName(),
                $this->getShowName()
            ),
            'last_name' => array(
                $this->getLastName(),
                $this->getShowLastName()
            ),
            'sex' => array(
                $this->getSex(),
                $this->getShowSex()
            ),
            'date_of_birth' => array(
                $this->getDateOfBirth(),
                $this->getShowDateOfBirth()
            ),
            'home_address' => array(
                $this->getHomeAddress(),
                $this->getShowHomeAddress()
            ),
            'term_address' => array(
                $this->getTermAddress(),
                $this->getShowTermAddress()
            ),
            'phone' => array(
                $this->getPhone(),
                $this->getShowPhone()
            ),
            'mobile' => array(
                $this->getMobile(),
                $this->getShowMobile(),
            ),
            'email' => array(
                $this->getEmail(),
                $this->getShowEmail(),
            ),
            'previously_assessed' => array(
                $this->getPreviouslyAssessed(),
                $this->getShowPreviouslyAssessed()
            ),
            'date' => array(
                $this->getDate(),
                $this->getShowDate(),
            ),
            'dsa_assessed_email' => array(
                $this->getDsaAssessedEmail(),
                $this->getShowDsaAssessedEmail(),
            ),
            'dsa_assessed_attachement' => array(
                $this->getDsaAssessedAttachement(),
                $this->getShowDsaAssessedAttachement(),
            ),
            'student_finance_england' => array(
                $this->getStudentFinanceEngland(),
                $this->getShowStudentFinanceEngland(),
            ),
            'sfw' => array(
                $this->getSfw(),
                $this->getShowSfw(),
            ),
            'sfni' => array(
                $this->getSfni(),
                $this->getShowSfni(),
            ),
            'saas' => array(
                $this->getSaas(),
                $this->getShowSaas(),
            ),
            'nhs' => array(
                $this->getNhs(),
                $this->getShowNhs(),
            ),
            'dsa_eligibility_letter' => array(
                $this->getDsaEligibilityLetter(),
                $this->getShowDsaEligibilityLetter(),
            ),
            'customer_reference_number' => array(
                $this->getCustomerReferenceNumber(),
                $this->getShowCustomerReferenceNumber(),
            ),
            'course_title' => array(
                $this->getCourseTitle(),
                $this->getShowCourseTitle(),
            ),
            'course_type' => array(
                $this->getCourseType(),
                $this->getShowCourseType(),
            ),
            'select_type' => array(
                $this->getSelectType(),
                $this->getShowSelectType(),
            ),
            'year_of_study' => array(
                $this->getYearOfStudy(),
                $this->getShowYearOfStudy(),
            ),
            'course_dates' => array(
                $this->getCourseDates(),
                $this->getShowCourseDates(),
            ),
            'learning_name' => array(
                $this->getLearningName(),
                $this->getShowLearningName(),
            ),
            'learning_address' => array(
                $this->getLearningAddress(),
                $this->getShowLearningAddress(),
            ),
            'disability_team_contact' => array(
                $this->getDisabilityTeamContact(),
                $this->getShowDisabilityTeamContact(),
            ),
            'disability_team_tel' => array(
                $this->getDisabilityTeamTel(),
                $this->getShowDisabilityTeamTel(),
            ),
            'disability_team_email' => array(
                $this->getDisabilityTeamEmail(),
                $this->getShowDisabilityTeamEmail(),
            ),
            'course_leader_contact' => array(
                $this->getCourseLeaderContact(),
                $this->getShowCourseLeaderContact(),
            ),
            'course_leader_tel' => array(
                $this->getCourseLeaderTel(),
                $this->getShowCourseLeaderTel(),
            ),
            'course_leader_email' => array(
                $this->getCourseLeaderEmail(),
                $this->getShowCourseLeaderEmail(),
            ),
            'permission_share' => array(
                $this->getPermissionShare(),
                $this->getShowPermissionShare(),
            ),
            'permission' => array(
                $this->getPermission(),
                $this->getShowPermission(),
            ),
            'type_disability' => array(
                $this->getTypeDisability(),
                $this->getShowTypeDisability(),
            ),
            'main_difficulties' => array(
                $this->getMainDifficulties(),
                $this->getShowMainDifficulties(),
            ),
            'type_of_support' => array(
                $this->getTypeOfSupport(),
                $this->getShowTypeOfSupport(),
            ),
            'type_of_equipment' => array(
                $this->getTypeOfEquipment(),
                $this->getShowTypeOfEquipment(),
            ),
            'special_access_requirements' => array(
                $this->getSpecialAccessRequirements(),
                $this->getShowSpecialAccessRequirements(),
            ),
            'dsa_eligibility_letter_current' => array(
                $this->getDsaEligibilityLetterCurrent(),
                $this->getShowDsaEligibilityLetterCurrent(),
            ),
            'diagnostic_assessment_documents' => array(
                $this->getDiagnosticAssessmentDocuments(),
                $this->getShowDiagnosticAssessmentDocuments(),
            ),
            'assurance_and_training' => array(
                $this->getAssuranceAndTraining(),
                $this->getShowAssuranceAndTraining(),
            ),
            'given_name' => array(
                $this->getGivenName(),
                $this->getShowGivenName(),
            ),
            'last_name_req' => array(
                $this->getLastNameReq(),
                $this->getShowLastNameReq()
            )
        );
    }
}
