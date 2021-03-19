<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole($role)
    {
        return $this->createQueryBuilder()
            ->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
            ->getQuery()
            ->getResult();
    }


    public function findOneByRpToken($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.rp_token = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneByEmailOrUsername($value): ?User
    {
        if (strpos($value,'@')===false) {
            return $this->findOneByUsername($value);
        }else{
            return $this->findOneByEmail($value);
        }
    }

    public function findOneByUsername($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findOneByEmail($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getUserFromWordpressOAuth(string $wordpressID, string $wordpressUsername, string $email): ?User
    {
        $user = $this->findOneByEmail($email);
        if (!$user){
            return null;
        }
        if ($user->getWordpressID() !== $wordpressID){
            $user = $this->updateUserWithWordpressData($wordpressID,$wordpressUsername,$user);
        }
        return $user;
    }

    public function updateUserWithWordpressData(string $wordpressID, string $wordpressUsername, User $user): ?User
    {
        $user->setWordpressID($wordpressID)
            ->setWordpressUsername($wordpressUsername);
        $this->_em->flush();

        return $user;
    }

    public function createUserFromWordpressOAuth(string $wordpressId,string $wordpressUsername,string $email,string $randomPassword): ?User
    {
        $user = new User();
        $user->setWordpressUsername($wordpressUsername)
            ->setWordpressID($wordpressId)
            ->setUsername($wordpressUsername)
            ->setEmail($email)
            ->setIsActive(true)
            ->setPassword($randomPassword);

        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

}
