<?php

namespace App\Repository;

use App\Entity\MainValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MainValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainValue[]    findAll()
 * @method MainValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainValue::class);
    }

    // /**
    //  * @return MainValue[] Returns an array of MainValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MainValue
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
