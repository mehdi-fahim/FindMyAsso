<?php

namespace App\Repository;

use App\Entity\InKindDonation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InKindDonation>
 *
 * @method InKindDonation|null find($id, $lockMode = null, $lockVersion = null)
 * @method InKindDonation[]    findAll()
 * @method InKindDonation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method InKindDonation|null findOneBy(array $criteria, array $orderBy = null)
 */
class InKindDonationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InKindDonation::class);
    }

    public function save(InKindDonation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InKindDonation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les dons par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * Trouve les dons par utilisateur
     */
    public function findByUser($user): array
    {
        return $this->findBy(['user' => $user]);
    }

    /**
     * Trouve les dons par association
     */
    public function findByAssociation($association): array
    {
        return $this->findBy(['association' => $association]);
    }

    /**
     * Trouve les dons par type
     */
    public function findByType(string $type): array
    {
        return $this->findBy(['type' => $type]);
    }

    /**
     * Trouve les dons par région
     */
    public function findByRegion(string $region): array
    {
        return $this->findBy(['region' => $region]);
    }

    /**
     * Trouve les dons par ville
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city]);
    }

    /**
     * Trouve les dons par type et région
     */
    public function findByTypeAndRegion(string $type, string $region): array
    {
        return $this->findBy([
            'type' => $type,
            'region' => $region
        ]);
    }

    /**
     * Trouve les dons par type et ville
     */
    public function findByTypeAndCity(string $type, string $city): array
    {
        return $this->findBy([
            'type' => $type,
            'city' => $city
        ]);
    }

    /**
     * Trouve les dons par association et type
     */
    public function findByAssociationAndType($association, string $type): array
    {
        return $this->findBy([
            'association' => $association,
            'type' => $type
        ]);
    }

    /**
     * Trouve les dons par utilisateur et type
     */
    public function findByUserAndType($user, string $type): array
    {
        return $this->findBy([
            'user' => $user,
            'type' => $type
        ]);
    }

    /**
     * Trouve les dons par période
     */
    public function findByPeriod(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.createdAt >= :from')
           ->andWhere('d.createdAt <= :to')
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('d.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par utilisateur et période
     */
    public function findByUserAndPeriod($user, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.user = :user')
           ->andWhere('d.createdAt >= :from')
           ->andWhere('d.createdAt <= :to')
           ->setParameter('user', $user)
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('d.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par association et période
     */
    public function findByAssociationAndPeriod($association, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.association = :association')
           ->andWhere('d.createdAt >= :from')
           ->andWhere('d.createdAt <= :to')
           ->setParameter('association', $association)
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('d.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons récents
     */
    public function findRecent(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->orderBy('d.createdAt', 'DESC')
           ->setMaxResults($limit);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par mois et année
     */
    public function findByMonthAndYear(int $month, int $year): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('MONTH(d.createdAt) = :month')
           ->andWhere('YEAR(d.createdAt) = :year')
           ->setParameter('month', $month)
           ->setParameter('year', $year)
           ->orderBy('d.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons généraux (pas ciblés à une association)
     */
    public function findGeneral(): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.association IS NULL');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par description (recherche partielle)
     */
    public function findByDescriptionLike(string $description): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.description LIKE :description')
           ->setParameter('description', '%' . $description . '%')
           ->orderBy('d.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par quantité (recherche partielle)
     */
    public function findByQuantityLike(string $quantity): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.quantity LIKE :quantity')
           ->setParameter('quantity', '%' . $quantity . '%')
           ->orderBy('d.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('d');

        if (isset($criteria['status'])) {
            $qb->andWhere('d.status = :status')
               ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['type'])) {
            $qb->andWhere('d.type = :type')
               ->setParameter('type', $criteria['type']);
        }

        if (isset($criteria['user'])) {
            $qb->andWhere('d.user = :user')
               ->setParameter('user', $criteria['user']);
        }

        if (isset($criteria['association'])) {
            $qb->andWhere('d.association = :association')
               ->setParameter('association', $criteria['association']);
        }

        if (isset($criteria['region'])) {
            $qb->andWhere('d.region = :region')
               ->setParameter('region', $criteria['region']);
        }

        if (isset($criteria['city'])) {
            $qb->andWhere('d.city = :city')
               ->setParameter('city', $criteria['city']);
        }

        if (isset($criteria['fromDate'])) {
            $qb->andWhere('d.createdAt >= :fromDate')
               ->setParameter('fromDate', $criteria['fromDate']);
        }

        if (isset($criteria['toDate'])) {
            $qb->andWhere('d.createdAt <= :toDate')
               ->setParameter('toDate', $criteria['toDate']);
        }

        if (isset($criteria['generalOnly'])) {
            if ($criteria['generalOnly']) {
                $qb->andWhere('d.association IS NULL');
            }
        }

        if (isset($criteria['targetedOnly'])) {
            if ($criteria['targetedOnly']) {
                $qb->andWhere('d.association IS NOT NULL');
            }
        }

        $qb->orderBy('d.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons correspondants à un besoin (matching)
     */
    public function findMatchingDonations(string $type, string $region, string $city): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.type = :type')
           ->andWhere('d.status = :status')
           ->andWhere('d.region = :region')
           ->andWhere('d.city = :city')
           ->setParameter('type', $type)
           ->setParameter('status', InKindDonation::STATUS_OFFERED)
           ->setParameter('region', $region)
           ->setParameter('city', $city)
           ->orderBy('d.createdAt', 'ASC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par statut et période
     */
    public function findByStatusAndPeriod(string $status, \DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.status = :status')
           ->andWhere('d.createdAt >= :from')
           ->andWhere('d.createdAt <= :to')
           ->setParameter('status', $status)
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->orderBy('d.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Compte les dons par statut
     */
    public function countByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }

    /**
     * Compte les dons par type
     */
    public function countByType(string $type): int
    {
        return $this->count(['type' => $type]);
    }

    /**
     * Compte les dons par utilisateur
     */
    public function countByUser($user): int
    {
        return $this->count(['user' => $user]);
    }

    /**
     * Compte les dons par association
     */
    public function countByAssociation($association): int
    {
        return $this->count(['association' => $association]);
    }

    /**
     * Compte les dons par région
     */
    public function countByRegion(string $region): int
    {
        return $this->count(['region' => $region]);
    }

    /**
     * Compte les dons par ville
     */
    public function countByCity(string $city): int
    {
        return $this->count(['city' => $city]);
    }
}
