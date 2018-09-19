<?php

namespace App\Repository;

use App\Entity\AssessmentCenter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use \Doctrine\Common\Collections\Criteria;

class AssessmentCenterRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, AssessmentCenter::class);
    }
    
    public function isUniqueField($id, $name, $value) {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->neq('id', $id))->andWhere(Criteria::expr()->eq($name, $value));
        $res = $this->matching($criteria)->count();
        return $res === 0 ? true : false;
    }

}
