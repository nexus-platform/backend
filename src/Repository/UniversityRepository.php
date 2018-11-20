<?php

namespace App\Repository;

use App\Entity\University;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UniversityRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, University::class);
    }
    
    public function getActiveUniversitiesByCountry($country) {
        return $this->createQueryBuilder('u')
                        ->where('u.country = :country')->setParameter('country', $country)
                        ->andWhere('u.manager is not null')
                        ->select(['u.id as value', 'u.name as text'])
                        ->orderBy('u.name')
                        ->getQuery()
                        ->getResult();
    }
    
    public function isUnique($id, $name, $value) {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->neq('id', $id))->andWhere(Criteria::expr()->eq($name, $value));
        $res = $this->matching($criteria)->count();
        return $res === 0 ? true : false;
    }

}
