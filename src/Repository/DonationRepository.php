<?php

namespace App\Repository;

use App\Entity\Donation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Donation>
 *
 * @method Donation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donation[]    findAll()
 * @method Donation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Donation|null findOneBy(array $criteria, array $orderBy = null)
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donation::class);
    }

    public function save(Donation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Donation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les dons payés
     */
    public function findPaid(): array
    {
        return $this->findBy(['status' => Donation::STATUS_PAID]);
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
     * Trouve les dons par statut
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy(['status' => $status]);
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
     * Trouve les dons par montant minimum
     */
    public function findByMinAmount(int $minAmount): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.amount >= :minAmount')
           ->setParameter('minAmount', $minAmount)
           ->orderBy('d.amount', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par montant maximum
     */
    public function findByMaxAmount(int $maxAmount): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.amount <= :maxAmount')
           ->setParameter('maxAmount', $maxAmount)
           ->orderBy('d.amount', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les dons par devise
     */
    public function findByCurrency(string $currency): array
    {
        return $this->findBy(['currency' => $currency]);
    }

    /**
     * Trouve les dons anonymes
     */
    public function findAnonymous(): array
    {
        $qb = $this->createQueryBuilder('d');
        $qb->where('d.user IS NULL');
        
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
     * Trouve les dons par Stripe Checkout ID
     */
    public function findByStripeCheckoutId(string $stripeCheckoutId): ?Donation
    {
        return $this->findOneBy(['stripeCheckoutId' => $stripeCheckoutId]);
    }

    /**
     * Trouve les dons par Payment Intent ID
     */
    public function findByStripePaymentIntentId(string $stripePaymentIntentId): ?Donation
    {
        return $this->findOneBy(['stripePaymentIntentId' => $stripePaymentIntentId]);
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
     * Calcule le total des dons payés
     */
    public function getTotalPaidAmount(): float
    {
        $qb = $this->createQueryBuilder('d');
        $qb->select('SUM(d.amount)')
           ->where('d.status = :status')
           ->setParameter('status', Donation::STATUS_PAID);
        
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ? $result / 100 : 0;
    }

    /**
     * Calcule le total des dons payés par association
     */
    public function getTotalPaidAmountByAssociation($association): float
    {
        $qb = $this->createQueryBuilder('d');
        $qb->select('SUM(d.amount)')
           ->where('d.status = :status')
           ->andWhere('d.association = :association')
           ->setParameter('status', Donation::STATUS_PAID)
           ->setParameter('association', $association);
        
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ? $result / 100 : 0;
    }

    /**
     * Calcule le total des dons payés par période
     */
    public function getTotalPaidAmountByPeriod(\DateTimeInterface $from, \DateTimeInterface $to): float
    {
        $qb = $this->createQueryBuilder('d');
        $qb->select('SUM(d.amount)')
           ->where('d.status = :status')
           ->andWhere('d.createdAt >= :from')
           ->andWhere('d.createdAt <= :to')
           ->setParameter('status', Donation::STATUS_PAID)
           ->setParameter('from', $from)
           ->setParameter('to', $to);
        
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ? $result / 100 : 0;
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

        if (isset($criteria['user'])) {
            $qb->andWhere('d.user = :user')
               ->setParameter('user', $criteria['user']);
        }

        if (isset($criteria['association'])) {
            $qb->andWhere('d.association = :association')
               ->setParameter('association', $criteria['association']);
        }

        if (isset($criteria['currency'])) {
            $qb->andWhere('d.currency = :currency')
               ->setParameter('currency', $criteria['currency']);
        }

        if (isset($criteria['fromDate'])) {
            $qb->andWhere('d.createdAt >= :fromDate')
               ->setParameter('fromDate', $criteria['fromDate']);
        }

        if (isset($criteria['toDate'])) {
            $qb->andWhere('d.createdAt <= :toDate')
               ->setParameter('toDate', $criteria['toDate']);
        }

        if (isset($criteria['minAmount'])) {
            $qb->andWhere('d.amount >= :minAmount')
               ->setParameter('minAmount', $criteria['minAmount']);
        }

        if (isset($criteria['maxAmount'])) {
            $qb->andWhere('d.amount <= :maxAmount')
               ->setParameter('maxAmount', $criteria['maxAmount']);
        }

        $qb->orderBy('d.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
