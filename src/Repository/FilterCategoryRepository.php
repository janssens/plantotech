<?php

namespace App\Repository;

use App\Entity\FilterCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FilterCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilterCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilterCategory[]    findAll()
 * @method FilterCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilterCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilterCategory::class);
    }

    // /**
    //  * @return FilterCategory[] Returns an array of FilterCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FilterCategory
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
