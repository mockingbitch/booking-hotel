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
     * @param int $id
     * @return Amount|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByRoomId(int $id): ?Amount
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.room = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param string $first
     * @param string $last
     *
     * @return array
     */
    public function dateRange(string $first, string $last) : array
    {
        $step = '+1 day';
        $format = 'Y-m-d';
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }


    /**
     * @param int $room_id
     * @param \DateTime $date
     *
     * @return array|null
     */
    public function findByDay(int $room_id,\DateTime $date) : array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.room = :val')
            ->andWhere('a.day = :day')
            ->setParameter('val', $room_id)
            ->setParameter('day', $date)
            ->getQuery()
            ->getResult()
            ;
    }
}
