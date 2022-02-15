<?php

/*
 * (c) Sven Nolting, 2022
 */

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

    /**
     * Returns active payer information.
     *
     * @return array<string, int>|null ['id' => 0, 'cnt' => 0]
     */
    public function findActivePayer()
    {
        $val = $this
            ->createQueryBuilder('p')
            ->select('count(p.paypalme) as cnt')
            ->join('p.paypalme', 'pm')
            ->addSelect('pm.id')
            ->where('p.created BETWEEN :date_start AND :date_end')
            ->groupBy('p.paypalme')
            ->orderBy('cnt', 'DESC')
            ->setParameter('date_start', strftime('%Y-%m-%d').' 00:00:00')
            ->setParameter('date_end', strftime('%Y-%m-%d').' 23:59:59')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        if (is_array($val) && count($val) > 0) {
            return $val[0];
        }

        return null;
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
