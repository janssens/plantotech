<?php

namespace App\Repository;

use App\Entity\PlantFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlantFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlantFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlantFamily[]    findAll()
 * @method PlantFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantFamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlantFamily::class);
    }

    // /**
    //  * @return PlantFamily[] Returns an array of PlantFamily objects
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

    /**
     * @return PlantFamily[] Returns an array of PlantFamily objects
    */
    public function findAlphabetical()
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?PlantFamily
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
