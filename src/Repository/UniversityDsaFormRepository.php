<?php

namespace App\Repository;

use App\Entity\UniversityDsaForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UniversityDsaFormRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, UniversityDsaForm::class);
    }

}
