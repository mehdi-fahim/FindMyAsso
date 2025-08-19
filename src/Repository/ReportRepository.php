<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 *
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function save(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les signalements ouverts
     */
    public function findOpen(): array
    {
        return $this->findBy(['status' => Report::STATUS_OPEN]);
    }

    /**
     * Trouve tous les signalements en cours d'examen
     */
    public function findUnderReview(): array
    {
        return $this->findBy(['status' => Report::STATUS_REVIEWING]);
    }

    /**
     * Trouve tous les signalements fermés
     */
    public function findClosed(): array
    {
        return $this->findBy(['status' => Report::STATUS_CLOSED]);
    }

    /**
     * Trouve les signalements par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * Trouve les signalements par type de cible
     */
    public function findByTargetType(string $targetType): array
    {
        return $this->findBy(['targetType' => $targetType]);
    }

    /**
     * Trouve les signalements par cible spécifique
     */
    public function findByTarget(string $targetType, string $targetId): array
    {
        return $this->findBy([
            'targetType' => $targetType,
            'targetId' => $targetId
        ]);
    }

    /**
     * Trouve les signalements par rapporteur
     */
    public function findByReporter($reporter): array
    {
        return $this->findBy(['reporter' => $reporter]);
    }

    /**
     * Trouve les signalements par période
     */
    public function findByPeriod(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.createdAt >= :from')
           ->andWhere('r.createdAt <= :to')
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('r.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les signalements urgents (plus de 7 jours)
     */
    public function findUrgent(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.status IN (:statuses)')
           ->andWhere('r.createdAt <= :urgentDate')
           ->setParameter('statuses', [Report::STATUS_OPEN, Report::STATUS_REVIEWING])
           ->setParameter('urgentDate', new \DateTime('-7 days'))
           ->orderBy('r.createdAt', 'ASC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les signalements récents
     */
    public function findRecent(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->orderBy('r.createdAt', 'DESC')
           ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les signalements par mois et année
     */
    public function findByMonthAndYear(int $month, int $year): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('MONTH(r.createdAt) = :month')
           ->andWhere('YEAR(r.createdAt) = :year')
           ->setParameter('month', $month)
           ->setParameter('year', $year)
           ->orderBy('r.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les signalements par raison (recherche partielle)
     */
    public function findByReasonLike(string $reason): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.reason LIKE :reason')
           ->setParameter('reason', '%' . $reason . '%')
           ->orderBy('r.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les signalements par action administrative
     */
    public function findByAdminAction(string $adminAction): array
    {
        return $this->findBy(['adminAction' => $adminAction]);
    }

    /**
     * Trouve les signalements sans action administrative
     */
    public function findWithoutAdminAction(): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.adminAction IS NULL OR r.adminAction = :empty')
           ->setParameter('empty', '');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les signalements par rapporteur et période
     */
    public function findByReporterAndPeriod($reporter, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('r');
        $qb->where('r.reporter = :reporter')
           ->andWhere('r.createdAt >= :from')
           ->andWhere('r.createdAt <= :to')
           ->setParameter('reporter', $reporter)
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('r.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les signalements par cible et statut
     */
    public function findByTargetAndStatus(string $targetType, string $targetId, string $status): array
    {
        return $this->findBy([
            'targetType' => $targetType,
            'targetId' => $targetId,
            'status' => $status
        ]);
    }

    /**
     * Trouve les signalements avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('r');

        if (isset($criteria['status'])) {
            $qb->andWhere('r.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['targetType'])) {
            $qb->andWhere('r.targetType = :targetType')
               ->setParameter('targetType', $criteria['targetType']);
        }

        if (isset($criteria['targetId'])) {
            $qb->andWhere('r.targetId = :targetId')
               ->setParameter('targetId', $criteria['targetId']);
        }

        if (isset($criteria['reporter'])) {
            $qb->andWhere('r.reporter = :reporter')
               ->setParameter('reporter', $criteria['reporter']);
        }

        if (isset($criteria['fromDate'])) {
            $qb->andWhere('r.createdAt >= :fromDate')
               ->setParameter('fromDate', $criteria['fromDate']);
        }

        if (isset($criteria['toDate'])) {
            $qb->andWhere('r.createdAt <= :toDate')
               ->setParameter('toDate', $criteria['toDate']);
        }

        if (isset($criteria['adminAction'])) {
            $qb->andWhere('r.adminAction = :adminAction')
               ->setParameter('adminAction', $criteria['adminAction']);
        }

        if (isset($criteria['urgentOnly'])) {
            if ($criteria['urgentOnly']) {
                $qb->andWhere('r.status IN (:urgentStatuses)')
                   ->andWhere('r.createdAt <= :urgentDate')
                   ->setParameter('urgentStatuses', [Report::STATUS_OPEN, Report::STATUS_REVIEWING])
                   ->setParameter('urgentDate', new \DateTime('-7 days'));
            }
        }

        $qb->orderBy('r.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte les signalements par statut
     */
    public function countByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }

    /**
     * Compte les signalements par type de cible
     */
    public function countByTargetType(string $targetType): int
    {
        return $this->count(['targetType' => $targetType]);
    }

    /**
     * Compte les signalements urgents
     */
    public function countUrgent(): int
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('COUNT(r.id)')
           ->where('r.status IN (:statuses)')
           ->andWhere('r.createdAt <= :urgentDate')
           ->setParameter('statuses', [Report::STATUS_OPEN, Report::STATUS_REVIEWING])
           ->setParameter('urgentDate', new \DateTime('-7 days'));
        
        return $qb->getQuery()->getSingleScalarResult();
    }
}
