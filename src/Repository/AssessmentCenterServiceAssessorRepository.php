<?php

namespace App\Repository;

use App\Entity\AssessmentCenterServiceAssessor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AssessmentCenterServiceAssessorRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, AssessmentCenterServiceAssessor::class);
    }

}
