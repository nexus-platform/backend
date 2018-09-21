<?php

namespace App\Repository;

use App\Entity\EaAppointment;
use App\Entity\User;
use App\Utils\StaticMembers;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EaAppointmentRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, EaAppointment::class);
    }

    public function getAppointmentsByAssessorAndDate($assessor, $date) {
        $res = $this->createQueryBuilder('t')
                ->where('t.provider = :provider')->setParameter('provider', $assessor)
                ->andWhere("t.start_datetime like :date or t.end_datetime like :date")->setParameter('date', "$date%")
                ->getQuery()
                ->getResult();
        return $res;
    }
    
    public function getAppointmentsByUser($user) {
        $res = $this->createQueryBuilder('t')
                ->where('t.provider = :user')->setParameter('user', $user)
                ->orWhere('t.student = :user')->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        return $res;
    }
    
    public function isAssessorAvailableByDate(User $assessor, DateTime $date) {
        $startDate = $date->format('Y-m-d H:i');
        return StaticMembers::executeRawSQL($this->_em, "select * from `ea_appointment` `t` where `provider_id` = " . $assessor->getId() . " and '$startDate' BETWEEN `t`.`start_datetime` and DATE_ADD(`t`.`end_datetime`, INTERVAL -1 second)", true);
    }

}
