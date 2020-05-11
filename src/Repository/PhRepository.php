<?php

namespace App\Repository;

use App\Entity\Ph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ph|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ph|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ph[]    findAll()
 * @method Ph[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ph::class);
    }

    // /**
    //  * @return Ph[] Returns an array of Ph objects
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
    public function findOneBySomeField($value): ?Ph
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
