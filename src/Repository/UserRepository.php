<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
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

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->save($user, true);
    }

    /**
     * Trouve un utilisateur par son email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Trouve tous les utilisateurs avec un rôle spécifique
     */
    public function findByRole(string $role): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where('JSON_CONTAINS(u.roles, :role) = 1')
           ->setParameter('role', '"' . $role . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve tous les utilisateurs non vérifiés
     */
    public function findUnverifiedUsers(): array
    {
        return $this->findBy(['isVerified' => false]);
    }

    /**
     * Trouve tous les utilisateurs vérifiés
     */
    public function findVerifiedUsers(): array
    {
        return $this->findBy(['isVerified' => true]);
    }

    /**
     * Trouve les utilisateurs par région
     */
    public function findByRegion(string $region): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.association', 'a')
           ->leftJoin('u.fosterProfile', 'f')
           ->leftJoin('u.vetProfile', 'v')
           ->where('a.region = :region OR f.region = :region OR v.region = :region')
           ->setParameter('region', $region);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les utilisateurs par ville
     */
    public function findByCity(string $city): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.association', 'a')
           ->leftJoin('u.fosterProfile', 'f')
           ->leftJoin('u.vetProfile', 'v')
           ->where('a.city = :city OR f.city = :city OR v.city = :city')
           ->setParameter('city', $city);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les utilisateurs avec des associations approuvées
     */
    public function findWithApprovedAssociations(): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->innerJoin('u.association', 'a')
           ->where('a.isApproved = :approved')
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les utilisateurs avec des profils vétérinaires approuvés
     */
    public function findWithApprovedVetProfiles(): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->innerJoin('u.vetProfile', 'v')
           ->where('v.isApproved = :approved')
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les utilisateurs par département
     */
    public function findByDepartment(string $department): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.association', 'a')
           ->leftJoin('u.fosterProfile', 'f')
           ->leftJoin('u.vetProfile', 'v')
           ->where('a.department = :department OR f.department = :department OR v.department = :department')
           ->setParameter('department', $department);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les utilisateurs avec des profils de famille d'accueil visibles
     */
    public function findWithVisibleFosterProfiles(): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->innerJoin('u.fosterProfile', 'f')
           ->where('f.isVisible = :visible')
           ->setParameter('visible', true);
        
        return $qb->getQuery()->getResult();
    }
}
