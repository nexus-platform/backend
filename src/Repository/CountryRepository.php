<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CountryRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Country::class);
    }

    public function orderBy($value) {
        return $this->createQueryBuilder('c')
                        ->orderBy($value, 'ASC')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function getActiveCountries() {
        return $this->createQueryBuilder('c')
                        ->join('App\Entity\University', 'u')
                        ->where('u.country = c')
                        ->andWhere('u.manager is not null')
                        ->select(['c.id as value', 'c.name as text'])
                        ->distinct()
                        ->orderBy('c.name')
                        ->getQuery()
                        ->getResult();
    }

    /*
      public function findBySomething($value)
      {
      return $this->createQueryBuilder('c')
      ->where('c.something = :value')->setParameter('value', $value)
      ->orderBy('c.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */
}
