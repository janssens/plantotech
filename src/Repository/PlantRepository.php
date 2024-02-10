<?php

namespace App\Repository;

use App\Entity\Plant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Plant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plant[]    findAll()
 * @method Plant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plant::class);
    }

    public function findByString($value): ?array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('concat(p.latin_name,p.name) like :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('p.id', 'ASC')
            //->setMaxResults(20)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByRusticity($value): ?array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.rusticity <= :val')
            ->setParameter('val', intval($value))
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Plant[] Returns an array of Plant objects
     */
    public function findAlphabetical()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Plant[] Returns an array of Plant objects
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
    public function findOneBySomeField($value): ?Plant
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
