<?php

namespace App\Repository;

use App\Entity\ShelterCapacity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShelterCapacity>
 *
 * @method ShelterCapacity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShelterCapacity[]    findAll()
 * @method ShelterCapacity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method ShelterCapacity|null findOneBy(array $criteria, array $orderBy = null)
 */
class ShelterCapacityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShelterCapacity::class);
    }

    public function save(ShelterCapacity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShelterCapacity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve toutes les capacités par espèce
     */
    public function findBySpecies(string $species): array
    {
        return $this->findBy(['species' => $species]);
    }

    /**
     * Trouve les capacités par association
     */
    public function findByAssociation($association): array
    {
        return $this->findBy(['association' => $association]);
    }

    /**
     * Trouve les capacités par association et espèce
     */
    public function findByAssociationAndSpecies($association, string $species): ?ShelterCapacity
    {
        return $this->findOneBy([
            'association' => $association,
            'species' => $species
        ]);
    }

    /**
     * Trouve les capacités avec de l'espace disponible
     */
    public function findWithAvailableSpace(): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->where('sc.capacityAvailable > 0');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités par espèce avec de l'espace disponible
     */
    public function findWithAvailableSpaceBySpecies(string $species): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->where('sc.species = :species')
           ->andWhere('sc.capacityAvailable > 0')
           ->setParameter('species', $species);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités par région avec de l'espace disponible
     */
    public function findWithAvailableSpaceByRegion(string $region): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.capacityAvailable > 0')
           ->andWhere('a.region = :region')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('region', $region)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités par ville avec de l'espace disponible
     */
    public function findWithAvailableSpaceByCity(string $city): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.capacityAvailable > 0')
           ->andWhere('a.city = :city')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('city', $city)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités par département avec de l'espace disponible
     */
    public function findWithAvailableSpaceByDepartment(string $department): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.capacityAvailable > 0')
           ->andWhere('a.department = :department')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('department', $department)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités par espèce et région avec de l'espace disponible
     */
    public function findWithAvailableSpaceBySpeciesAndRegion(string $species, string $region): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.species = :species')
           ->andWhere('sc.capacityAvailable > 0')
           ->andWhere('a.region = :region')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('species', $species)
           ->setParameter('region', $region)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités par espèce et ville avec de l'espace disponible
     */
    public function findWithAvailableSpaceBySpeciesAndCity(string $species, string $city): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.species = :species')
           ->andWhere('sc.capacityAvailable > 0')
           ->andWhere('a.city = :city')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('species', $species)
           ->setParameter('city', $city)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités par espèce et département avec de l'espace disponible
     */
    public function findWithAvailableSpaceBySpeciesAndDepartment(string $species, string $department): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.species = :species')
           ->andWhere('sc.capacityAvailable > 0')
           ->andWhere('a.department = :department')
           ->andWhere('a.isApproved = :approved')
           ->setParameter('species', $species)
           ->setParameter('department', $department)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités dans un rayon donné avec de l'espace disponible
     */
    public function findWithAvailableSpaceByRadius(float $lat, float $lng, float $radiusKm): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.capacityAvailable > 0')
           ->andWhere('a.isApproved = :approved')
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
     * Trouve les capacités par espèce dans un rayon donné avec de l'espace disponible
     */
    public function findWithAvailableSpaceBySpeciesAndRadius(string $species, float $lat, float $lng, float $radiusKm): array
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->innerJoin('sc.association', 'a')
           ->where('sc.species = :species')
           ->andWhere('sc.capacityAvailable > 0')
           ->andWhere('a.isApproved = :approved')
           ->andWhere('a.lat IS NOT NULL')
           ->andWhere('a.lng IS NOT NULL')
           ->andWhere('(
               (6371 * acos(cos(radians(:lat)) * cos(radians(a.lat)) * 
                cos(radians(a.lng) - radians(:lng)) + sin(radians(:lat)) * 
                sin(radians(a.lat)))) <= :radius
           )')
           ->setParameter('species', $species)
           ->setParameter('approved', true)
           ->setParameter('lat', $lat)
           ->setParameter('lng', $lng)
           ->setParameter('radius', $radiusKm);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les capacités avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('sc');

        if (isset($criteria['species'])) {
            $qb->andWhere('sc.species = :species')
               ->setParameter('species', $criteria['species']);
        }

        if (isset($criteria['association'])) {
            $qb->andWhere('sc.association = :association')
               ->setParameter('association', $criteria['association']);
        }

        if (isset($criteria['hasAvailableSpace'])) {
            if ($criteria['hasAvailableSpace']) {
                $qb->andWhere('sc.capacityAvailable > 0');
            }
        }

        if (isset($criteria['region'])) {
            $qb->innerJoin('sc.association', 'a')
               ->andWhere('a.region = :region')
               ->andWhere('a.isApproved = :approved')
               ->setParameter('region', $criteria['region'])
               ->setParameter('approved', true);
        }

        if (isset($criteria['city'])) {
            $qb->innerJoin('sc.association', 'a')
               ->andWhere('a.city = :city')
               ->andWhere('a.isApproved = :approved')
               ->setParameter('city', $criteria['city'])
               ->setParameter('approved', true);
        }

        if (isset($criteria['department'])) {
            $qb->innerJoin('sc.association', 'a')
               ->andWhere('a.department = :department')
               ->andWhere('a.isApproved = :approved')
               ->setParameter('department', $criteria['department'])
               ->setParameter('approved', true);
        }

        if (isset($criteria['minCapacity'])) {
            $qb->andWhere('sc.capacityTotal >= :minCapacity')
               ->setParameter('minCapacity', $criteria['minCapacity']);
        }

        if (isset($criteria['maxCapacity'])) {
            $qb->andWhere('sc.capacityTotal <= :maxCapacity')
               ->setParameter('maxCapacity', $criteria['maxCapacity']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte les capacités par espèce
     */
    public function countBySpecies(string $species): int
    {
        return $this->count(['species' => $species]);
    }

    /**
     * Compte les capacités par association
     */
    public function countByAssociation($association): int
    {
        return $this->count(['association' => $association]);
    }

    /**
     * Compte les capacités avec de l'espace disponible
     */
    public function countWithAvailableSpace(): int
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->select('COUNT(sc.id)')
           ->where('sc.capacityAvailable > 0');
        
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Compte les capacités par espèce avec de l'espace disponible
     */
    public function countWithAvailableSpaceBySpecies(string $species): int
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->select('COUNT(sc.id)')
           ->where('sc.species = :species')
           ->andWhere('sc.capacityAvailable > 0')
           ->setParameter('species', $species);
        
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Calcule la capacité totale par espèce
     */
    public function getTotalCapacityBySpecies(string $species): int
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->select('SUM(sc.capacityTotal)')
           ->where('sc.species = :species')
           ->setParameter('species', $species);
        
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ?: 0;
    }

    /**
     * Calcule la capacité disponible totale par espèce
     */
    public function getTotalAvailableCapacityBySpecies(string $species): int
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->select('SUM(sc.capacityAvailable)')
           ->where('sc.species = :species')
           ->setParameter('species', $species);
        
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ?: 0;
    }

    /**
     * Calcule la capacité utilisée totale par espèce
     */
    public function getTotalUsedCapacityBySpecies(string $species): int
    {
        $qb = $this->createQueryBuilder('sc');
        $qb->select('SUM(sc.capacityTotal - sc.capacityAvailable)')
           ->where('sc.species = :species')
           ->setParameter('species', $species);
        
        $result = $qb->getQuery()->getSingleScalarResult();
        return $result ?: 0;
    }
}
