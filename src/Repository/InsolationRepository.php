<?php

namespace App\Repository;

use App\Entity\Insolation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Insolation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Insolation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Insolation[]    findAll()
 * @method Insolation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InsolationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Insolation::class);
    }

    // /**
    //  * @return Insolation[] Returns an array of Insolation objects
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
    public function findOneBySomeField($value): ?Insolation
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
