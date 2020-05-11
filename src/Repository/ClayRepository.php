<?php

namespace App\Repository;

use App\Entity\Clay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Clay|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clay[]    findAll()
 * @method Clay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clay::class);
    }

    // /**
    //  * @return Clay[] Returns an array of Clay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Clay
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
