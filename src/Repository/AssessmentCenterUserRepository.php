<?php

namespace App\Repository;

use App\Entity\AssessmentCenterUser;
use App\Utils\StaticMembers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AssessmentCenterUserRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, AssessmentCenterUser::class);
    }

    public function getActiveACs() {
        $data = $this->createQueryBuilder('t')
                        ->where('t.is_admin = 1')
                        ->distinct()
                        ->getQuery()
                        ->getResult();
        $res = [];
        foreach ($data as $acu) {
            $ac = $acu->getAc();
            if (!StaticMembers::contains($res, $ac)) {
                $res[] = $ac;
            }
        }
        return $res;
    }
    
    public function getACAdmin($ac) {
        $data = $this->createQueryBuilder('t')
                        ->where('t.ac = :ac')->setParameter('ac', $ac)
                        ->andWhere('t.is_admin = 1')
                        ->getQuery()
                        ->getResult();
        $res = $data[0]->getUser();
        return $res;
    }
    
    public function getWorkingPlansForOtherACs($user, $ac) {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('user', $user))->andWhere(Criteria::expr()->neq('ac', $ac));
        $res = $this->matching($criteria)->toArray();
        return $res;
    }
    
    public function getUsersByAC($ac) {
        $res = $this->createQueryBuilder('t')
                        ->join('t.user', 'user')
                        ->where('t.ac = :ac')->setParameter('ac', $ac)
                        ->getQuery()->getResult();
        return $res;
    }
    
    public function getStudentsByAC($ac) {
        $res = $this->createQueryBuilder('t')
                        //->join('t.user', 'user')
                        ->where('t.ac = :ac')->setParameter('ac', $ac)
                        //->andWhere('user.isStudent = :isStudent')->setParameter('isStudent', true)
                        ->getQuery()->getResult();
        return $res;
    }

}
