<?php

namespace App\Repository;

use App\Entity\AdoptionRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdoptionRequest>
 *
 * @method AdoptionRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdoptionRequest[]    findAll()
 * @method AdoptionRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method AdoptionRequest|null findOneBy(array $criteria, array $orderBy = null)
 */
class AdoptionRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdoptionRequest::class);
    }

    public function save(AdoptionRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdoptionRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve toutes les nouvelles demandes
     */
    public function findNew(): array
    {
        return $this->findBy(['status' => AdoptionRequest::STATUS_NEW]);
    }

    /**
     * Trouve toutes les demandes en cours d'examen
     */
    public function findUnderReview(): array
    {
        return $this->findBy(['status' => AdoptionRequest::STATUS_UNDER_REVIEW]);
    }

    /**
     * Trouve toutes les demandes acceptées
     */
    public function findAccepted(): array
    {
        return $this->findBy(['status' => AdoptionRequest::STATUS_ACCEPTED]);
    }

    /**
     * Trouve toutes les demandes refusées
     */
    public function findRejected(): array
    {
        return $this->findBy(['status' => AdoptionRequest::STATUS_REJECTED]);
    }

    /**
     * Trouve toutes les demandes annulées
     */
    public function findCancelled(): array
    {
        return $this->findBy(['status' => AdoptionRequest::STATUS_CANCELLED]);
    }

    /**
     * Trouve les demandes par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * Trouve les demandes par animal
     */
    public function findByAnimal($animal): array
    {
        return $this->findBy(['animal' => $animal]);
    }

    /**
     * Trouve les demandes par animal et statut
     */
    public function findByAnimalAndStatus($animal, string $status): array
    {
        return $this->findBy([
            'animal' => $animal,
            'status' => $status
        ]);
    }

    /**
     * Trouve les demandes par demandeur
     */
    public function findByRequester($requester): array
    {
        return $this->findBy(['requester' => $requester]);
    }

    /**
     * Trouve les demandes par demandeur et statut
     */
    public function findByRequesterAndStatus($requester, string $status): array
    {
        return $this->findBy([
            'requester' => $requester,
            'status' => $status
        ]);
    }

    /**
     * Trouve les demandes par association
     */
    public function findByAssociation($association): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->innerJoin('ar.animal', 'a')
           ->where('a.association = :association')
           ->setParameter('association', $association)
           ->orderBy('ar.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes par association et statut
     */
    public function findByAssociationAndStatus($association, string $status): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->innerJoin('ar.animal', 'a')
           ->where('a.association = :association')
           ->andWhere('ar.status = :status')
           ->setParameter('association', $association)
           ->setParameter('status', $status)
           ->orderBy('ar.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes par période
     */
    public function findByPeriod(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->where('ar.createdAt >= :from')
           ->andWhere('ar.createdAt <= :to')
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('ar.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes par animal et période
     */
    public function findByAnimalAndPeriod($animal, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->where('ar.animal = :animal')
           ->andWhere('ar.createdAt >= :from')
           ->andWhere('ar.createdAt <= :to')
           ->setParameter('animal', $animal)
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('ar.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes par demandeur et période
     */
    public function findByRequesterAndPeriod($requester, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->where('ar.requester = :requester')
           ->andWhere('ar.createdAt >= :from')
           ->andWhere('ar.createdAt <= :to')
           ->setParameter('requester', $requester)
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('ar.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes récentes
     */
    public function findRecent(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->orderBy('ar.createdAt', 'DESC')
           ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes par mois et année
     */
    public function findByMonthAndYear(int $month, int $year): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->where('MONTH(ar.createdAt) = :month')
           ->andWhere('YEAR(ar.createdAt) = :year')
           ->setParameter('month', $month)
           ->setParameter('year', $year)
           ->orderBy('ar.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes par animal et demandeur
     */
    public function findByAnimalAndRequester($animal, $requester): ?AdoptionRequest
    {
        return $this->findOneBy([
            'animal' => $animal,
            'requester' => $requester
        ]);
    }

    /**
     * Trouve les demandes en attente de réponse
     */
    public function findPendingResponse(): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->where('ar.status IN (:statuses)')
           ->setParameter('statuses', [AdoptionRequest::STATUS_NEW, AdoptionRequest::STATUS_UNDER_REVIEW])
           ->orderBy('ar.createdAt', 'ASC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes complétées
     */
    public function findCompleted(): array
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->where('ar.status IN (:statuses)')
           ->setParameter('statuses', [AdoptionRequest::STATUS_ACCEPTED, AdoptionRequest::STATUS_REJECTED, AdoptionRequest::STATUS_CANCELLED])
           ->orderBy('ar.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les demandes avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('ar');

        if (isset($criteria['status'])) {
            $qb->andWhere('ar.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['animal'])) {
            $qb->andWhere('ar.animal = :animal')
               ->setParameter('animal', $criteria['animal']);
        }

        if (isset($criteria['requester'])) {
            $qb->andWhere('ar.requester = :requester')
               ->setParameter('requester', $criteria['requester']);
        }

        if (isset($criteria['association'])) {
            $qb->innerJoin('ar.animal', 'a')
               ->andWhere('a.association = :association')
               ->setParameter('association', $criteria['association']);
        }

        if (isset($criteria['fromDate'])) {
            $qb->andWhere('ar.createdAt >= :fromDate')
               ->setParameter('fromDate', $criteria['fromDate']);
        }

        if (isset($criteria['toDate'])) {
            $qb->andWhere('ar.createdAt <= :toDate')
               ->setParameter('toDate', $criteria['toDate']);
        }

        if (isset($criteria['pendingOnly'])) {
            if ($criteria['pendingOnly']) {
                $qb->andWhere('ar.status IN (:pendingStatuses)')
                   ->setParameter('pendingStatuses', [AdoptionRequest::STATUS_NEW, AdoptionRequest::STATUS_UNDER_REVIEW]);
            }
        }

        if (isset($criteria['completedOnly'])) {
            if ($criteria['completedOnly']) {
                $qb->andWhere('ar.status IN (:completedStatuses)')
                   ->setParameter('completedStatuses', [AdoptionRequest::STATUS_ACCEPTED, AdoptionRequest::STATUS_REJECTED, AdoptionRequest::STATUS_CANCELLED]);
            }
        }

        $qb->orderBy('ar.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte les demandes par statut
     */
    public function countByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }

    /**
     * Compte les demandes par animal
     */
    public function countByAnimal($animal): int
    {
        return $this->count(['animal' => $animal]);
    }

    /**
     * Compte les demandes par demandeur
     */
    public function countByRequester($requester): int
    {
        return $this->count(['requester' => $requester]);
    }

    /**
     * Compte les demandes en attente de réponse
     */
    public function countPendingResponse(): int
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->select('COUNT(ar.id)')
           ->where('ar.status IN (:statuses)')
           ->setParameter('statuses', [AdoptionRequest::STATUS_NEW, AdoptionRequest::STATUS_UNDER_REVIEW]);
        
        return $qb->getQuery()->getSingleScalarResult();
    }
}
