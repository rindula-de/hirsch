<?php

namespace App\Repository;

use App\Entity\Payhistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Payhistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payhistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payhistory[]    findAll()
 * @method Payhistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayhistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payhistory::class);
    }

    // /**
    //  * @return Payhistory[] Returns an array of Payhistory objects
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
    public function findOneBySomeField($value): ?Payhistory
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
