<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function findByEmail($value)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :value')->setParameter('value', $value)
            ->getQuery()
            ->getFirstResult()
        ;
    }
}
