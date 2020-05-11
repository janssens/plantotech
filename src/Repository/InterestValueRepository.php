<?php

namespace App\Repository;

use App\Entity\InterestValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InterestValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterestValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterestValue[]    findAll()
 * @method InterestValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterestValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterestValue::class);
    }

    // /**
    //  * @return InterestValue[] Returns an array of InterestValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InterestValue
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
