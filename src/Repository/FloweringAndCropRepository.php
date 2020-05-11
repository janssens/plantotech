<?php

namespace App\Repository;

use App\Entity\FloweringAndCrop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FloweringAndCrop|null find($id, $lockMode = null, $lockVersion = null)
 * @method FloweringAndCrop|null findOneBy(array $criteria, array $orderBy = null)
 * @method FloweringAndCrop[]    findAll()
 * @method FloweringAndCrop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FloweringAndCropRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FloweringAndCrop::class);
    }

    // /**
    //  * @return FloweringAndCrop[] Returns an array of FloweringAndCrop objects
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
    public function findOneBySomeField($value): ?FloweringAndCrop
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
