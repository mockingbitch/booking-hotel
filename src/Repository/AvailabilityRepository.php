<?php

namespace App\Repository;

use App\Entity\Availability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Availability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Availability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Availability[]    findAll()
 * @method Availability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Availability::class);
    }

    // /**
    //  * @return Availability[] Returns an array of Availability objects
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
    public function findOneBySomeField($value): ?Availability
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
     * @param int $room
     * @param \DateTime $date
     *
     * @return array|null
     */
    public function findRoom(int $room, \DateTime $date) : array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.day = :date')
            ->andWhere('a.room = :id')
            ->setParameter('date', $date)
            ->setParameter('id', $room)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $room_id
     * @param \DateTime $date
     *
     * @return array|null
     */
    public function findByDay(int $room_id, \DateTime $date) : array
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
