<?php

namespace App\Repository;

use App\Entity\DsaFormFilled;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DsaFormFilledRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, DsaFormFilled::class);
    }
    
    public function getUnfinishedForms($user, $dsaForm) {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('user', $user));
        $criteria->andWhere(Criteria::expr()->eq('dsaForm', $dsaForm));
        $criteria->andWhere(Criteria::expr()->in('status', [0, 2]));
        $criteria->orderBy(['created_at' => 'DESC']);
        $res = $this->matching($criteria)->toArray();
        return $res;
    }

}
