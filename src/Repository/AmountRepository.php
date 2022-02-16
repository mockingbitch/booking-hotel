<?php

namespace App\Repository;

use App\Entity\Amount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Amount|null find($id, $lockMode = null, $lockVersion = null)
 * @method Amount|null findOneBy(array $criteria, array $orderBy = null)
 * @method Amount[]    findAll()
 * @method Amount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Amount::class);
    }

    // /**
    //  * @return Amount[] Returns an array of Amount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Amount
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param $id
     * @return Amount|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByRoomId($id): ?Amount
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.room = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $first
     * @param $last
     * @param $step
     * @param $format
     *
     * @return array
     */
    function dateRange($first, $last) {
        $step = '+1 day';
        $format = 'Y-m-d';
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

//    /**
//     * @param $room_id
//     * @param $start_date
//     * @param $end_date
//     *
//     * @return float|int|mixed|string|null
//     * @throws \Doctrine\ORM\NonUniqueResultException
//     */
//    public function findByDayRange($room_id,$start_date,$end_date)
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.room = :val')
//            ->andWhere('a.day >= :start')
//            ->andWhere('a.day <= :end')
//            ->setParameter('val', $room_id)
//            ->setParameter('start',$start_date)
//            ->setParameter('end',$end_date)
//            ->getQuery()
//            ->getOneOrNullResult()
//            ;
//    }

    /**
     * @param $room_id
     * @param $date
     *
     * @return float|int|mixed|string
     */
    public function findByDay($room_id,$date)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.room = :val')
            ->andWhere('a.day = :day')
            ->setParameter('val', $room_id)
            ->setParameter('day',$date)
            ->getQuery()
            ->getResult()
            ;
    }
}
