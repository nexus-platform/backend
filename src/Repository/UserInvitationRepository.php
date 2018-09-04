<?php

namespace App\Repository;

use App\Entity\UserInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserInvitationRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, UserInvitation::class);
    }

    public function findByEmail($value) {
        return $this->createQueryBuilder('u')
                        ->where('u.email = :value')->setParameter('value', $value)
                        ->getQuery()
                        ->getArrayResult()
        ;
    }

}
