<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    // /**
    //  * @return Room[] Returns an array of Room objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Room
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

//    /**
//     * @param int $room_id
//     *
//     * @return float|int|mixed|string
//     */
//    public function findById(int $room_id) :array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.id = :val')
//            ->setParameter('val', $room_id)
//            ->getQuery()
//            ->getOneOrNullResult()
//            ;
//    }

    /**
     * @param string|null $name
     * @param float|null $min
     * @param float|null $max
     * @param string|null $start_date
     * @param string|null $end_date
     *
     * @return array
     */
    public function findByFields(?string $name, ?float $min, ?float $max, ?string $start_date, ?string $end_date) : array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r.id', 'r.name', 'av.stock', 'am.price', 'av.day')
            ->leftJoin('App\Entity\Availability', 'av', \Doctrine\ORM\Query\Expr\Join::WITH, 'av.room = r')
            ->leftJoin('App\Entity\Amount', 'am', \Doctrine\ORM\Query\Expr\Join::WITH, 'am.room = r')
            ->andWhere('am.day = av.day');

        if (null !== $name) {
            $qb->andWhere('r.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        if (null !== $min && null !== $max) {
            $qb->andWhere('am.price BETWEEN :min AND :max')
                ->setParameter('min', $min)
                ->setParameter('max', $max);
        }

        if (null !== $start_date && null !== $end_date) {
            $qb->andWhere('am.day BETWEEN :from AND :to')
                ->setParameter('from', $start_date)
                ->setParameter('to', $end_date);
        }

        return $qb->getQuery()->getResult();
    }
}
