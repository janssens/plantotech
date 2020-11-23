<?php

namespace App\Repository;

use App\Entity\Attribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attribute[]    findAll()
 * @method Attribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    /**
     * @return Attribute Returns an attribute or null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByCode($code)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.code = :val')
            ->setParameter('val', $code)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
