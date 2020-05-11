<?php

namespace App\Repository;

use App\Entity\NeedValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NeedValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method NeedValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method NeedValue[]    findAll()
 * @method NeedValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NeedValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NeedValue::class);
    }

    // /**
    //  * @return NeedValue[] Returns an array of NeedValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NeedValue
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
