<?php

namespace App\Repository;

use App\Entity\EaAppointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EaAppointmentRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, EaAppointment::class);
    }

    public function getAppointmentsByAssessorAndDate($assessor, $date) {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.provider = :provider')->setParameter('provider', $assessor)
                //->andWhere($qb->expr()->orX($qb->expr()->like('t.start_datetime', "$date%"), $qb->expr()->like('t.end_datetime', "$date%")));
                ->andWhere("t.start_datetime like :date or t.end_datetime like :date")->setParameter('date', "$date%");
                

        /* $criteria = new Criteria();
          $criteria->where(Criteria::expr()->eq('provider', $assessor));
          $criteria->andWhere(
          $criteria->expr()->orX(
          $criteria->expr()->startsWith('start_datetime', $date), $criteria->expr()->contains('end_datetime', $date)
          )
          );
          $res = $this->matching($criteria)->toArray(); */
        /*$res =  $this->createQueryBuilder('t')
                ->where('t.provider = :provider')
                ->setParameter('provider', $assessor)
                        ->getQuery()
                        ->getResult();*/
        $res = $qb->getQuery()->getResult();
        return $res;
    }

}
