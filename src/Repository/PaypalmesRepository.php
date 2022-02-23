<?php

/*
 * (c) Sven Nolting, 2022
 */

namespace App\Repository;

use App\Entity\Paypalmes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paypalmes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paypalmes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paypalmes[]    findAll()
 * @method Paypalmes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaypalmesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paypalmes::class);
    }

    // /**
    //  * @return Paypalmes[] Returns an array of Paypalmes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Paypalmes
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
