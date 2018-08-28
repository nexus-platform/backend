<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DsaSlimRepository")
 * @ApiResource
 */
class DsaSlim
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DisabilityOfficer", inversedBy="dsa_slim", cascade={"persist"})
     */
    private $center;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $customer_reference_number;

    /**
     * @ORM\Column(type="string")
     */
    private $forename;

    /**
     * @ORM\Column(type="string")
     */
    private $surname;

    /**
     * @ORM\Column(type="string")
     */
    private $sex;

    /**
     * @ORM\Column(type="string")
     */
    private $dobDay;

    /**
     * @ORM\Column(type="string")
     */
    private $dobMonth;

    /**
     * @ORM\Column(type="string")
     */
    private $dobYear;

    /**
     * @ORM\Column(type="string")
     */
    private $excluding;

    /**
     * @ORM\Column(type="string")
     */
    private $saas;

    /**
     * @ORM\Column(type="string")
     */
    private $healthcare;

    /**
     * @ORM\Column(type="string")
     */
    private $receipt;

    /**
     * @ORM\Column(type="string")
     */
    private $motability_car;

    /**
     * @ORM\Column(type="string")
     */
    private $disabilitydetails;

    /**
     * @var UploadedFile
     * @ORM\Column(type="string")
     */
    private $disabilitydetailsfile;

    /**
     * @var UploadedFile
     * @ORM\Column(type="string")
     */
    private $long_termadverse_effect;

    /**
     * @var UploadedFile
     * @ORM\Column(type="string")
     */
    private $learning_difficulty;

    /**
     * @var UploadedFile
     * @ORM\Column(type="string")
     */
    private $autistic_spectrum_disorders;

    /**
     * @ORM\Column(type="string")
     */
    private $laDay;

    /**
     * @ORM\Column(type="string")
     */
    private $laMonth;

    /**
     * @ORM\Column(type="string")
     */
    private $laYear;

    /**
     * @ORM\Column(type="string")
     */
    private $pc;

    /**
     * @ORM\Column(type="string")
     */
    private $working_order;

    /**
     * @ORM\Column(type="string")
     */
    private $model;

    /**
     * @ORM\Column(type="string")
     */
    private $age;

    /**
     * @ORM\Column(type="string")
     */
    private $processor;

    /**
     * @ORM\Column(type="string")
     */
    private $agree1;

    /**
     * @ORM\Column(type="string")
     */
    private $agree2;

    /**
     * @ORM\Column(type="string")
     */
    private $agree3;

    /**
     * @ORM\Column(type="string")
     */
    private $sortcode;

    /**
     * @ORM\Column(type="string")
     */
    private $accountnumber;

    /**
     * @ORM\Column(type="string")
     */
    private $building;

    /**
     * @ORM\Column(type="string")
     */
    private $fullname;

    /**
     * @ORM\Column(type="string")
     */
    private $todayDay;

    /**
     * @ORM\Column(type="string")
     */
    private $todayMonth;

    /**
     * @ORM\Column(type="string")
     */
    private $todayYear;

    /**
     * @ORM\Column(type="string")
     */
    private $signed;


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
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * @param mixed $forename
     */
    public function setForename($forename)
    {
        $this->forename = $forename;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
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
    public function getDobDay()
    {
        return $this->dobDay;
    }

    /**
     * @param mixed $dobDay
     */
    public function setDobDay($dobDay)
    {
        $this->dobDay = $dobDay;
    }

    /**
     * @return mixed
     */
    public function getDobMonth()
    {
        return $this->dobMonth;
    }

    /**
     * @param mixed $dobMonth
     */
    public function setDobMonth($dobMonth)
    {
        $this->dobMonth = $dobMonth;
    }

    /**
     * @return mixed
     */
    public function getDobYear()
    {
        return $this->dobYear;
    }

    /**
     * @param mixed $dobYear
     */
    public function setDobYear($dobYear)
    {
        $this->dobYear = $dobYear;
    }

    /**
     * @return mixed
     */
    public function getExcluding()
    {
        return $this->excluding;
    }

    /**
     * @param mixed $excluding
     */
    public function setExcluding($excluding)
    {
        $this->excluding = $excluding;
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
    public function getHealthcare()
    {
        return $this->healthcare;
    }

    /**
     * @param mixed $healthcare
     */
    public function setHealthcare($healthcare)
    {
        $this->healthcare = $healthcare;
    }

    /**
     * @return mixed
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @param mixed $receipt
     */
    public function setReceipt($receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * @return mixed
     */
    public function getMotabilityCar()
    {
        return $this->motability_car;
    }

    /**
     * @param mixed $motability_car
     */
    public function setMotabilityCar($motability_car)
    {
        $this->motability_car = $motability_car;
    }

    /**
     * @return mixed
     */
    public function getDisabilitydetails()
    {
        return $this->disabilitydetails;
    }

    /**
     * @param mixed $disabilitydetails
     */
    public function setDisabilitydetails($disabilitydetails)
    {
        $this->disabilitydetails = $disabilitydetails;
    }

    /**
     * @return UploadedFile
     */
    public function getDisabilitydetailsfile(): ?UploadedFile
    {
        return $this->disabilitydetailsfile;
    }

    /**
     * @param UploadedFile $disabilitydetailsfile
     */
    public function setDisabilitydetailsfile(UploadedFile $disabilitydetailsfile)
    {
        $this->disabilitydetailsfile = $disabilitydetailsfile;
    }

    /**
     * @return UploadedFile
     */
    public function getLongTermadverseEffect(): ?UploadedFile
    {
        return $this->long_termadverse_effect;
    }

    /**
     * @param UploadedFile $long_termadverse_effect
     */
    public function setLongTermadverseEffect(UploadedFile $long_termadverse_effect)
    {
        $this->long_termadverse_effect = $long_termadverse_effect;
    }

    /**
     * @return UploadedFile
     */
    public function getLearningDifficulty(): ?UploadedFile
    {
        return $this->learning_difficulty;
    }

    /**
     * @param UploadedFile $learning_difficulty
     */
    public function setLearningDifficulty(UploadedFile $learning_difficulty)
    {
        $this->learning_difficulty = $learning_difficulty;
    }

    /**
     * @return UploadedFile
     */
    public function getAutisticSpectrumDisorders(): ?UploadedFile
    {
        return $this->autistic_spectrum_disorders;
    }

    /**
     * @param UploadedFile $autistic_spectrum_disorders
     */
    public function setAutisticSpectrumDisorders(UploadedFile $autistic_spectrum_disorders)
    {
        $this->autistic_spectrum_disorders = $autistic_spectrum_disorders;
    }

    /**
     * @return mixed
     */
    public function getLaDay()
    {
        return $this->laDay;
    }

    /**
     * @param mixed $laDay
     */
    public function setLaDay($laDay)
    {
        $this->laDay = $laDay;
    }

    /**
     * @return mixed
     */
    public function getLaMonth()
    {
        return $this->laMonth;
    }

    /**
     * @param mixed $laMonth
     */
    public function setLaMonth($laMonth)
    {
        $this->laMonth = $laMonth;
    }

    /**
     * @return mixed
     */
    public function getLaYear()
    {
        return $this->laYear;
    }

    /**
     * @param mixed $laYear
     */
    public function setLaYear($laYear)
    {
        $this->laYear = $laYear;
    }

    /**
     * @return mixed
     */
    public function getPc()
    {
        return $this->pc;
    }

    /**
     * @param mixed $pc
     */
    public function setPc($pc)
    {
        $this->pc = $pc;
    }

    /**
     * @return mixed
     */
    public function getWorkingOrder()
    {
        return $this->working_order;
    }

    /**
     * @param mixed $working_order
     */
    public function setWorkingOrder($working_order)
    {
        $this->working_order = $working_order;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * @param mixed $processor
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
    }

    /**
     * @return mixed
     */
    public function getAgree1()
    {
        return $this->agree1;
    }

    /**
     * @param mixed $agree1
     */
    public function setAgree1($agree1)
    {
        $this->agree1 = $agree1;
    }

    /**
     * @return mixed
     */
    public function getAgree2()
    {
        return $this->agree2;
    }

    /**
     * @param mixed $agree2
     */
    public function setAgree2($agree2)
    {
        $this->agree2 = $agree2;
    }

    /**
     * @return mixed
     */
    public function getAgree3()
    {
        return $this->agree3;
    }

    /**
     * @param mixed $agree3
     */
    public function setAgree3($agree3)
    {
        $this->agree3 = $agree3;
    }

    /**
     * @return mixed
     */
    public function getSortcode()
    {
        return $this->sortcode;
    }

    /**
     * @param mixed $sortcode
     */
    public function setSortcode($sortcode)
    {
        $this->sortcode = $sortcode;
    }

    /**
     * @return mixed
     */
    public function getAccountnumber()
    {
        return $this->accountnumber;
    }

    /**
     * @param mixed $accountnumber
     */
    public function setAccountnumber($accountnumber)
    {
        $this->accountnumber = $accountnumber;
    }

    /**
     * @return mixed
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param mixed $building
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * @return mixed
     */
    public function getTodayDay()
    {
        return $this->todayDay;
    }

    /**
     * @param mixed $todayDay
     */
    public function setTodayDay($todayDay)
    {
        $this->todayDay = $todayDay;
    }

    /**
     * @return mixed
     */
    public function getTodayMonth()
    {
        return $this->todayMonth;
    }

    /**
     * @param mixed $todayMonth
     */
    public function setTodayMonth($todayMonth)
    {
        $this->todayMonth = $todayMonth;
    }

    /**
     * @return mixed
     */
    public function getTodayYear()
    {
        return $this->todayYear;
    }

    /**
     * @param mixed $todayYear
     */
    public function setTodayYear($todayYear)
    {
        $this->todayYear = $todayYear;
    }

    /**
     * @return mixed
     */
    public function getSigned()
    {
        return $this->signed;
    }

    /**
     * @param mixed $signed
     */
    public function setSigned($signed)
    {
        $this->signed = $signed;
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
