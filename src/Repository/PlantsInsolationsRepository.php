<?php

namespace App\Repository;

use App\Entity\PlantsInsolations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlantsInsolations|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlantsInsolations|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlantsInsolations[]    findAll()
 * @method PlantsInsolations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantsInsolationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlantsInsolations::class);
    }

    // /**
    //  * @return PlantsPorts[] Returns an array of PlantsPorts objects
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
    public function findOneBySomeField($value): ?PlantsPorts
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
