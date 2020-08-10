<?php

namespace App\Repository;

use App\Entity\AttributeFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttributeFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeFamily[]    findAll()
 * @method AttributeFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeFamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeFamily::class);
    }

    // /**
    //  * @return AttributeFamily[] Returns an array of AttributeFamily objects
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
    public function findOneBySomeField($value): ?AttributeFamily
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
