<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;

class DBRepository {
    
    public static function getRolesByUserId(EntityManager $entityManager, $user_id) {
        $query = "select * from `role` r where `r`.`id` in (select `role_id` from `user_role` where `user_id` = $user_id) order by `r`.`name` asc";
        $statement = $entityManager->getConnection()->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll();
        $return = [];
        foreach ($results as $value) {
            $return[] = $value['slug'];
        }
        return $return;
    }

}
