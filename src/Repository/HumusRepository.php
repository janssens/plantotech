<?php

namespace App\Repository;

use App\Entity\Humus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Humus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Humus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Humus[]    findAll()
 * @method Humus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HumusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Humus::class);
    }

    // /**
    //  * @return Humus[] Returns an array of Humus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Humus
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
