<?php

namespace App\Repository;

use App\Entity\AssessmentCenter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AssessmentCenterRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, AssessmentCenter::class);
    }

}
