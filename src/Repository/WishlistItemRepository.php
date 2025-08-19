<?php

namespace App\Repository;

use App\Entity\WishlistItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WishlistItem>
 *
 * @method WishlistItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method WishlistItem[]    findAll()
 * @method WishlistItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method WishlistItem|null findOneBy(array $criteria, array $orderBy = null)
 */
class WishlistItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WishlistItem::class);
    }

    public function save(WishlistItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WishlistItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les éléments actifs
     */
    public function findActive(): array
    {
        return $this->findBy(['isActive' => true]);
    }

    /**
     * Trouve les éléments par association
     */
    public function findByAssociation($association): array
    {
        return $this->findBy(['association' => $association]);
    }

    /**
     * Trouve les éléments actifs par association
     */
    public function findActiveByAssociation($association): array
    {
        return $this->findBy(['association' => $association, 'isActive' => true]);
    }

    /**
     * Trouve les éléments par type
     */
    public function findByType(string $type): array
    {
        return $this->findBy(['type' => $type, 'isActive' => true]);
    }

    /**
     * Trouve les éléments par urgence
     */
    public function findByUrgency(string $urgency): array
{
        return $this->findBy(['urgency' => $urgency, 'isActive' => true]);
    }

    /**
     * Trouve les éléments urgents
     */
    public function findUrgent(): array
    {
        return $this->findBy(['urgency' => WishlistItem::URGENCY_HIGH, 'isActive' => true]);
    }

    /**
     * Trouve les éléments par association et type
     */
    public function findByAssociationAndType($association, string $type): array
    {
        return $this->findBy([
            'association' => $association,
            'type' => $type,
            'isActive' => true
        ]);
    }

    /**
     * Trouve les éléments par association et urgence
     */
    public function findByAssociationAndUrgency($association, string $urgency): array
    {
        return $this->findBy([
            'association' => $association,
            'urgency' => $urgency,
            'isActive' => true
        ]);
    }

    /**
     * Trouve les éléments par région
     */
    public function findByRegion(string $region): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('a.region = :region')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('active', true)
           ->setParameter('region', $region)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par ville
     */
    public function findByCity(string $city): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('a.city = :city')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('active', true)
           ->setParameter('city', $city)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par département
     */
    public function findByDepartment(string $department): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('a.department = :department')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('active', true)
           ->setParameter('department', $department)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par type et région
     */
    public function findByTypeAndRegion(string $type, string $region): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('w.type = :type')
           ->andWhere('a.region = :region')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('active', true)
           ->setParameter('type', $type)
           ->setParameter('region', $region)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par type et ville
     */
    public function findByTypeAndCity(string $type, string $city): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('w.type = :type')
           ->andWhere('a.city = :city')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('active', true)
           ->setParameter('type', $type)
           ->setParameter('city', $city)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par urgence et région
     */
    public function findByUrgencyAndRegion(string $urgency, string $region): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('w.urgency = :urgency')
           ->andWhere('a.region = :region')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('active', true)
           ->setParameter('urgency', $urgency)
           ->setParameter('region', $region)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par urgence et ville
     */
    public function findByUrgencyAndCity(string $urgency, string $city): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('w.urgency = :urgency')
           ->andWhere('a.city = :city')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('active', true)
           ->setParameter('urgency', $urgency)
           ->setParameter('city', $city)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments dans un rayon donné
     */
    public function findByRadius(float $lat, float $lng, float $radiusKm): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->innerJoin('w.association', 'a')
           ->where('w.isActive = :active')
           ->andWhere('a.isApproved = :approved')
           ->andWhere('a.lat IS NOT NULL')
           ->andWhere('a.lng IS NOT NULL')
           ->andWhere('(
               (6371 * acos(cos(radians(:lat)) * cos(radians(a.lat)) * 
                cos(radians(a.lng) - radians(:lng)) + sin(radians(:lat)) * 
                sin(radians(a.lat)))) <= :radius
           )')
           ->setParameter('active', true)
           ->setParameter('approved', true)
           ->setParameter('lat', $lat)
           ->setParameter('lng', $lng)
           ->setParameter('radius', $radiusKm);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par nom (recherche partielle)
     */
    public function findByLabelLike(string $label): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->where('w.isActive = :active')
           ->andWhere('w.label LIKE :label')
           ->setParameter('active', true)
           ->setParameter('label', '%' . $label . '%');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments récents
     */
    public function findRecent(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->where('w.isActive = :active')
           ->orderBy('w.createdAt', 'DESC')
           ->setMaxResults($limit)
           ->setParameter('active', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments par mois et année
     */
    public function findByMonthAndYear(int $month, int $year): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->where('w.isActive = :active')
           ->andWhere('MONTH(w.createdAt) = :month')
           ->andWhere('YEAR(w.createdAt) = :year')
           ->setParameter('active', true)
           ->setParameter('month', $month)
           ->setParameter('year', $year)
           ->orderBy('w.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les éléments avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('w');
        $qb->where('w.isActive = :active')
           ->setParameter('active', true);

        if (isset($criteria['type'])) {
            $qb->andWhere('w.type = :type')
               ->setParameter('type', $criteria['type']);
        }

        if (isset($criteria['urgency'])) {
            $qb->andWhere('w.urgency = :urgency')
               ->setParameter('urgency', $criteria['urgency']);
        }

        if (isset($criteria['association'])) {
            $qb->andWhere('w.association = :association')
               ->setParameter('association', $criteria['association']);
        }

        if (isset($criteria['region'])) {
            $qb->innerJoin('w.association', 'a')
               ->andWhere('a.region = :region')
               ->andWhere('a.isApproved = :approved')
               ->setParameter('region', $criteria['region'])
               ->setParameter('approved', true);
        }

        if (isset($criteria['city'])) {
            $qb->innerJoin('w.association', 'a')
               ->andWhere('a.city = :city')
               ->andWhere('a.isApproved = :approved')
               ->setParameter('city', $criteria['city'])
               ->setParameter('approved', true);
        }

        if (isset($criteria['department'])) {
            $qb->innerJoin('w.association', 'a')
               ->andWhere('a.department = :department')
               ->andWhere('a.isApproved = :approved')
               ->setParameter('department', $criteria['department'])
               ->setParameter('approved', true);
        }

        if (isset($criteria['urgentOnly'])) {
            if ($criteria['urgentOnly']) {
                $qb->andWhere('w.urgency = :urgent')
                   ->setParameter('urgent', WishlistItem::URGENCY_HIGH);
            }
        }

        if (isset($criteria['fromDate'])) {
            $qb->andWhere('w.createdAt >= :fromDate')
               ->setParameter('fromDate', $criteria['fromDate']);
        }

        if (isset($criteria['toDate'])) {
            $qb->andWhere('w.createdAt <= :toDate')
               ->setParameter('toDate', $criteria['toDate']);
        }

        $qb->orderBy('w.urgency', 'DESC')
           ->addOrderBy('w.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte les éléments par type
     */
    public function countByType(string $type): int
    {
        return $this->count(['type' => $type, 'isActive' => true]);
    }

    /**
     * Compte les éléments par urgence
     */
    public function countByUrgency(string $urgency): int
    {
        return $this->count(['urgency' => $urgency, 'isActive' => true]);
    }

    /**
     * Compte les éléments par association
     */
    public function countByAssociation($association): int
    {
        return $this->count(['association' => $association, 'isActive' => true]);
    }

    /**
     * Compte les éléments urgents
     */
    public function countUrgent(): int
    {
        return $this->count(['urgency' => WishlistItem::URGENCY_HIGH, 'isActive' => true]);
    }
}
