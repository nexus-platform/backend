<?php

namespace App\Repository;

use App\Entity\AssessmentCenter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AssessmentCenterRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, AssessmentCenter::class);
    }
    
    public function getAnotherACByName($ac) {
        return $this->createQueryBuilder('ac')
                        ->where('ac.name = :name')->setParameter('name', $ac->getName())
                        ->andWhere('ac.id != :id')->setParameter('id', $ac->getId())
                        ->getQuery()->getOneOrNullResult();
    }
    
    public function getAnotherACBySlug($ac) {
        return $this->createQueryBuilder('ac')
                        ->where('ac.url = :url')->setParameter('url', $ac->getUrl())
                        ->andWhere('ac.id != :id')->setParameter('id', $ac->getId())
                        ->getQuery()->getFirstResult();
    }

}
