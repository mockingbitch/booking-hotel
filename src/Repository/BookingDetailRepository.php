<?php

namespace App\Repository;

use App\Entity\BookingDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookingDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookingDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookingDetail[]    findAll()
 * @method BookingDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookingDetail::class);
    }

    // /**
    //  * @return BookingDetail[] Returns an array of BookingDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookingDetail
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
