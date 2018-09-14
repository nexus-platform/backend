<?php

namespace App\Repository;

use App\Entity\AssessmentCenterServiceAssessor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AssessmentCenterServiceAssessorRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, AssessmentCenterServiceAssessor::class);
    }

    public function findNAServicesByAC($ac, $assessor) {
        return $this->createQueryBuilder('acsa')
                        ->join('acsa.service', 'serv')
                        ->where('acsa.assessor = :assessor')->setParameter('assessor', $assessor)
                        ->andWhere('serv.ac = :ac')->setParameter('ac', $ac)
                        ->getQuery()->getResult();
    }

}
