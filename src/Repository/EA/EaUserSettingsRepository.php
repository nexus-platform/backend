<?php

namespace App\Repository\EA;

use App\Entity\EA\EaUserSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EaUserSettingsRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, EaUserSettings::class);
    }

}
