<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Repository;

use App\Entity\Hirsch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hirsch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hirsch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hirsch[]    findAll()
 * @method Hirsch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HirschRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hirsch::class);
    }

    // /**
    //  * @return Hirsch[] Returns an array of Hirsch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hirsch
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
