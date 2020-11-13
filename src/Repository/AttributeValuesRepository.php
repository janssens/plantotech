<?php

namespace App\Repository;

use App\Entity\AttributeValues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttributeValues|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeValues|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeValues[]    findAll()
 * @method AttributeValues[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeValuesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeValues::class);
    }

    // /**
    //  * @return AttributeValues[] Returns an array of AttributeValues objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AttributeValues
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
