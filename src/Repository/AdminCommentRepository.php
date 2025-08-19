<?php

namespace App\Repository;

use App\Entity\AdminComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdminComment>
 *
 * @method AdminComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminComment[]    findAll()
 * @method AdminComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method AdminComment|null findOneBy(array $criteria, array $orderBy = null)
 */
class AdminCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminComment::class);
    }

    public function save(AdminComment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdminComment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les commentaires pour une entité spécifique
     */
    public function findByEntity(string $entityType, string $entityId): array
    {
        return $this->findBy([
            'entityType' => $entityType,
            'entityId' => $entityId,
        ], ['createdAt' => 'DESC']);
    }

    /**
     * Trouve les commentaires par action
     */
    public function findByAction(string $action): array
    {
        return $this->findBy(['action' => $action], ['createdAt' => 'DESC']);
    }

    /**
     * Trouve les commentaires par administrateur
     */
    public function findByAdmin($admin): array
    {
        return $this->findBy(['admin' => $admin], ['createdAt' => 'DESC']);
    }

    /**
     * Trouve les commentaires récents (dernières 24h)
     */
    public function findRecent(): array
    {
        $yesterday = new \DateTime('-24 hours');
        
        return $this->createQueryBuilder('c')
            ->where('c.createdAt >= :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les commentaires par type d'entité et action
     */
    public function findByEntityTypeAndAction(string $entityType, string $action): array
    {
        return $this->findBy([
            'entityType' => $entityType,
            'action' => $action,
        ], ['createdAt' => 'DESC']);
    }

    /**
     * Trouve les commentaires d'approbation récents
     */
    public function findRecentApprovals(): array
    {
        $yesterday = new \DateTime('-24 hours');
        
        return $this->createQueryBuilder('c')
            ->where('c.action = :action')
            ->andWhere('c.createdAt >= :yesterday')
            ->setParameter('action', 'approval')
            ->setParameter('yesterday', $yesterday)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les commentaires de rejet récents
     */
    public function findRecentRejections(): array
    {
        $yesterday = new \DateTime('-24 hours');
        
        return $this->createQueryBuilder('c')
            ->where('c.action = :action')
            ->andWhere('c.createdAt >= :yesterday')
            ->setParameter('action', 'rejection')
            ->setParameter('yesterday', $yesterday)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
