<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Repository;

use App\Entity\MsUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MsUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method MsUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method MsUser[]    findAll()
 * @method MsUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MsUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MsUser::class);
    }

    // /**
    //  * @return MsUser[] Returns an array of MsUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MsUser
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
