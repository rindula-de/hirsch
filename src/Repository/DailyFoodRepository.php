<?php

/*
 * (c) Sven Nolting, 2023
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
    public function save(DailyFood $food, bool $flush = true): void
    {
        $this->getEntityManager()->persist($food);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array{'date': \DateTime, 'gericht': string}
     */
    public function getDailyFood(): array
    {
        $data = $this->createQueryBuilder('d')
            ->select('d.date', 'd.name as gericht')
            // limit to this week
            ->andWhere('d.date >= :start')
            ->setParameter('start', new \DateTime('monday this week'))
            ->getQuery()
            ->getResult();
        // set time of date to 14:00:00
        foreach ($data as $key => $value) {
            $data[$key]['date'] = $value['date']->setTime(14, 0, 0);
        }
        return $data;
    }
}
