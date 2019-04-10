<?php

namespace App\Repository\EA;

use App\Entity\EA\EaAppointments;
use App\Entity\User;
use App\Utils\StaticMembers;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EaAppointmentsRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, EaAppointments::class);
    }

    public function getAppointmentsByAssessorAndDate($assessor, $date) {
        $res = $this->createQueryBuilder('t')
                ->where('t.provider = :provider')->setParameter('provider', $assessor)
                ->andWhere("t.start_datetime like :date or t.end_datetime like :date")->setParameter('date', "$date%")
                ->getQuery()
                ->getResult();
        return $res;
    }

    public function getAppointmentsByUser($user, $all) {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.idUsersProvider = :user or t.idUsersCustomer = :user')->setParameter('user', $user);
        if (!$all) {
            $qb->andWhere('t.startDatetime >= :now')->setParameter('now', date('Y-m-d H:i:s', time()));
        }
        $res = $qb->getQuery()->getResult();
        return $res;
    }

    public function getAppointmentsByAC($ac, $all) {
        $qb = $this->createQueryBuilder('t');
        $qb->join('t.idServices', 'serv');
        $qb->where('serv.idAssessmentCenter = :ac')->setParameter('ac', $ac);
        if (!$all) {
            $qb->andWhere('t.startDatetime >= :now')->setParameter('now', date('Y-m-d H:i:s', time()));
        }
        $res = $qb->getQuery()->getResult();
        return $res;
        /*$res = $this->createQueryBuilder('t')
                        ->join('t.idServices', 'serv')
                        ->where('serv.idAssessmentCenter = :ac')->setParameter('ac', $ac)
                        ->andWhere('t.startDatetime >= :now')->setParameter('now', date('Y-m-d H:i:s', time()))
                        ->getQuery()->getResult();*/
    }

    public function isAssessorAvailableByDate(User $assessor, DateTime $date) {
        $startDate = $date->format('Y-m-d H:i');
        return StaticMembers::executeRawSQL($this->_em, "select * from `ea_appointment` `t` where `provider_id` = " . $assessor->getId() . " and '$startDate' BETWEEN `t`.`start_datetime` and DATE_ADD(`t`.`end_datetime`, INTERVAL -1 second)", true);
    }

    public function extendUnavailabilityByLowerLimit(User $assessor, DateTime $startDateTime, DateTime $endDateTime) {
        $startDate = $startDateTime->format('Y-m-d H:i');
        $endDate = $endDateTime->format('Y-m-d H:i');
        return StaticMembers::executeRawSQL($this->_em, "select * from `ea_appointment` `t` where `t`.`provider_id` = " . $assessor->getId() . " and `t`.`is_unavailable`= 1 and '$startDate' < `t`.`start_datetime` and '$endDate' >= `t`.`start_datetime`", true);
    }

    public function extendUnavailabilityByUpperLimit(User $assessor, DateTime $startDateTime, DateTime $endDateTime) {
        $startDate = $startDateTime->format('Y-m-d H:i');
        $endDate = $endDateTime->format('Y-m-d H:i');
        return StaticMembers::executeRawSQL($this->_em, "select * from `ea_appointment` `t` where `t`.`provider_id` = " . $assessor->getId() . " and `t`.`is_unavailable`= 1 and '$endDate' > `t`.`end_datetime` and '$startDate' <= `t`.`end_datetime`", true);
    }

    public function unavailabilityInRange(User $assessor, DateTime $startDateTime, DateTime $endDateTime) {
        $startDate = $startDateTime->format('Y-m-d H:i');
        $endDate = $endDateTime->format('Y-m-d H:i');
        $res = StaticMembers::executeRawSQL($this->_em, "select * from `ea_appointment` `t` where `t`.`provider_id` = " . $assessor->getId() . " and `t`.`is_unavailable`= 1 and '$startDate' >= `t`.`start_datetime` and '$endDate' <= `t`.`end_datetime`", true);
        return (count($res) > 0);
    }

}
