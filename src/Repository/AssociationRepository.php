<?php

namespace App\Repository;

use App\Entity\Association;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Association>
 *
 * @method Association|null find($id, $lockMode = null, $lockVersion = null)
 * @method Association|null findOneBy(array $criteria, array $orderBy = null)
 * @method Association[]    findAll()
 * @method Association[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Association::class);
    }

    public function save(Association $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Association $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve toutes les associations approuvées
     */
    public function findApproved(): array
    {
        return $this->findBy(['isApproved' => true]);
    }

    /**
     * Trouve les associations par région
     */
    public function findByRegion(string $region): array
    {
        return $this->findBy(['region' => $region, 'isApproved' => true]);
    }

    /**
     * Trouve les associations par département
     */
    public function findByDepartment(string $department): array
    {
        return $this->findBy(['department' => $department, 'isApproved' => true]);
    }

    /**
     * Trouve les associations par ville
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city, 'isApproved' => true]);
    }

    /**
     * Trouve les associations par espèce supportée
     */
    public function findBySpecies(string $species): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.isApproved = :approved')
           ->andWhere('JSON_CONTAINS(a.speciesSupported, :species) = 1')
           ->setParameter('approved', true)
           ->setParameter('species', '"' . $species . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations avec capacité disponible pour une espèce
     */
    public function findWithAvailableCapacity(string $species): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.shelterCapacities', 'sc')
           ->where('a.isApproved = :approved')
           ->andWhere('sc.species = :species')
           ->andWhere('sc.capacityAvailable > 0')
           ->setParameter('approved', true)
           ->setParameter('species', $species);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations par région et espèce
     */
    public function findByRegionAndSpecies(string $region, string $species): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.isApproved = :approved')
           ->andWhere('a.region = :region')
           ->andWhere('JSON_CONTAINS(a.speciesSupported, :species) = 1')
           ->setParameter('approved', true)
           ->setParameter('region', $region)
           ->setParameter('species', '"' . $species . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations par ville et espèce
     */
    public function findByCityAndSpecies(string $city, string $species): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.isApproved = :approved')
           ->andWhere('a.city = :city')
           ->andWhere('JSON_CONTAINS(a.speciesSupported, :species) = 1')
           ->setParameter('approved', true)
           ->setParameter('city', $city)
           ->setParameter('species', '"' . $species . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations dans un rayon donné (approximatif)
     */
    public function findByRadius(float $lat, float $lng, float $radiusKm): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.isApproved = :approved')
           ->andWhere('a.lat IS NOT NULL')
           ->andWhere('a.lng IS NOT NULL')
           ->andWhere('(
               (6371 * acos(cos(radians(:lat)) * cos(radians(a.lat)) * 
                cos(radians(a.lng) - radians(:lng)) + sin(radians(:lat)) * 
                sin(radians(a.lat)))) <= :radius
           )')
           ->setParameter('approved', true)
           ->setParameter('lat', $lat)
           ->setParameter('lng', $lng)
           ->setParameter('radius', $radiusKm);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations avec des animaux disponibles
     */
    public function findWithAvailableAnimals(): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.animals', 'an')
           ->where('a.isApproved = :approved')
           ->andWhere('an.status = :status')
           ->setParameter('approved', true)
           ->setParameter('status', 'AVAILABLE')
           ->groupBy('a.id');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations par code postal
     */
    public function findByPostalCode(string $postalCode): array
    {
        return $this->findBy(['postalCode' => $postalCode, 'isApproved' => true]);
    }

    /**
     * Trouve les associations avec des besoins urgents
     */
    public function findWithUrgentNeeds(): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.wishlistItems', 'w')
           ->where('a.isApproved = :approved')
           ->andWhere('w.isActive = :active')
           ->andWhere('w.urgency = :urgency')
           ->setParameter('approved', true)
           ->setParameter('active', true)
           ->setParameter('urgency', 'HIGH')
           ->groupBy('a.id');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations par nom (recherche partielle)
     */
    public function findByNameLike(string $name): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.isApproved = :approved')
           ->andWhere('a.name LIKE :name')
           ->setParameter('approved', true)
           ->setParameter('name', '%' . $name . '%');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les associations par propriétaire
     */
    public function findByOwner($owner): array
    {
        return $this->findBy(['owner' => $owner]);
    }

    /**
     * Trouve les associations en attente d'approbation
     */
    public function findPendingApproval(): array
    {
        return $this->findBy(['isApproved' => false]);
    }

    /**
     * Trouve les associations avec filtres et pagination
     */
    public function findByFilters(?string $region = null, ?string $species = null, int $page = 1, int $limit = 12): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.isApproved = :approved')
            ->setParameter('approved', true)
            ->orderBy('a.name', 'ASC');

        if ($region) {
            $qb->andWhere('a.region = :region')
               ->setParameter('region', $region);
        }

        if ($species) {
            $qb->andWhere('JSON_CONTAINS(a.speciesSupported, :species) = 1')
               ->setParameter('species', '"' . $species . '"');
        }

        $offset = ($page - 1) * $limit;
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve toutes les régions distinctes des associations approuvées
     */
    public function findAllRegions(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.region')
            ->where('a.isApproved = :approved')
            ->andWhere('a.region IS NOT NULL')
            ->andWhere('a.region != :empty')
            ->setParameter('approved', true)
            ->setParameter('empty', '')
            ->orderBy('a.region', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return array_column($result, 'region');
    }

    /**
     * Trouve toutes les espèces supportées distinctes des associations approuvées
     */
    public function findAllSpecies(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.speciesSupported')
            ->where('a.isApproved = :approved')
            ->andWhere('a.speciesSupported IS NOT NULL')
            ->setParameter('approved', true);

        $result = $qb->getQuery()->getScalarResult();
        $species = [];

        foreach ($result as $row) {
            if ($row['speciesSupported']) {
                $decoded = json_decode($row['speciesSupported'], true);
                if (is_array($decoded)) {
                    $species = array_merge($species, $decoded);
                }
            }
        }

        $species = array_unique($species);
        sort($species);
        return $species;
    }
}
