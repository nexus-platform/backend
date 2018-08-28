<?php

namespace App\Repository;

use App\Entity\University;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UniversityRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, University::class);
    }

    /*
      public function findBySomething($value)
      {
      return $this->createQueryBuilder('u')
      ->where('u.something = :value')->setParameter('value', $value)
      ->orderBy('u.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */
    
    public function getActiveUniversitiesByCountry($country) {
        return $this->createQueryBuilder('u')
                        ->where('u.country = :country')->setParameter('country', $country)
                        ->andWhere('u.manager is not null')
                        ->select(['u.id as value', 'u.name as text'])
                        ->orderBy('u.name')
                        ->getQuery()
                        ->getResult();
    }

}
