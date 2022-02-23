<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Repository;

use App\Entity\DailyFood;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DailyFood|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyFood|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyFood[]    findAll()
 * @method DailyFood[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyFoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyFood::class);
    }

    // /**
    //  * @return DailyFood[] Returns an array of DailyFood objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DailyFood
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
