<?php

namespace App\Repository;

use App\Entity\NeedType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NeedType|null find($id, $lockMode = null, $lockVersion = null)
 * @method NeedType|null findOneBy(array $criteria, array $orderBy = null)
 * @method NeedType[]    findAll()
 * @method NeedType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NeedTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NeedType::class);
    }

    // /**
    //  * @return NeedType[] Returns an array of NeedType objects
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
    public function findOneBySomeField($value): ?NeedType
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
