<?php

namespace App\Repository;

use App\Entity\FosterProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FosterProfile>
 *
 * @method FosterProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method FosterProfile[]    findAll()
 * @method FosterProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method FosterProfile|null findOneBy(array $criteria, array $orderBy = null)
 */
class FosterProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FosterProfile::class);
    }

    public function save(FosterProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FosterProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les profils visibles
     */
    public function findVisible(): array
    {
        return $this->findBy(['isVisible' => true]);
    }

    /**
     * Trouve les profils par région
     */
    public function findByRegion(string $region): array
    {
        return $this->findBy(['region' => $region, 'isVisible' => true]);
    }

    /**
     * Trouve les profils par département
     */
    public function findByDepartment(string $department): array
    {
        return $this->findBy(['department' => $department, 'isVisible' => true]);
    }

    /**
     * Trouve les profils par ville
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city, 'isVisible' => true]);
    }

    /**
     * Trouve les profils par espèce acceptée
     */
    public function findBySpeciesAccepted(string $species): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.isVisible = :visible')
           ->andWhere('JSON_CONTAINS(f.speciesAccepted, :species) = 1')
           ->setParameter('visible', true)
           ->setParameter('species', '"' . $species . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils par région et espèce
     */
    public function findByRegionAndSpecies(string $region, string $species): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.isVisible = :visible')
           ->andWhere('f.region = :region')
           ->andWhere('JSON_CONTAINS(f.speciesAccepted, :species) = 1')
           ->setParameter('visible', true)
           ->setParameter('region', $region)
           ->setParameter('species', '"' . $species . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils par ville et espèce
     */
    public function findByCityAndSpecies(string $city, string $species): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.isVisible = :visible')
           ->andWhere('f.city = :city')
           ->andWhere('JSON_CONTAINS(f.speciesAccepted, :species) = 1')
           ->setParameter('visible', true)
           ->setParameter('city', $city)
           ->setParameter('species', '"' . $species . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils dans un rayon donné
     */
    public function findByRadius(float $lat, float $lng, float $radiusKm): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.isVisible = :visible')
           ->andWhere('f.lat IS NOT NULL')
           ->andWhere('f.lng IS NOT NULL')
           ->andWhere('(
               (6371 * acos(cos(radians(:lat)) * cos(radians(f.lat)) * 
                cos(radians(f.lng) - radians(:lng)) + sin(radians(:lat)) * 
                sin(radians(f.lat)))) <= :radius
           )')
           ->setParameter('visible', true)
           ->setParameter('lat', $lat)
           ->setParameter('lng', $lng)
           ->setParameter('radius', $radiusKm);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils avec jardin
     */
    public function findWithGarden(): array
    {
        return $this->findBy(['hasGarden' => true, 'isVisible' => true]);
    }

    /**
     * Trouve les profils sans enfants
     */
    public function findWithoutChildren(): array
    {
        return $this->findBy(['childrenAtHome' => false, 'isVisible' => true]);
    }

    /**
     * Trouve les profils avec d'autres animaux
     */
    public function findWithOtherPets(): array
    {
        return $this->findBy(['otherPets' => true, 'isVisible' => true]);
    }

    /**
     * Trouve les profils disponibles actuellement
     */
    public function findCurrentlyAvailable(): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.isVisible = :visible')
           ->andWhere('(f.availabilityFrom IS NULL OR f.availabilityFrom <= :now)')
           ->andWhere('(f.availabilityTo IS NULL OR f.availabilityTo >= :now)')
           ->setParameter('visible', true)
           ->setParameter('now', new \DateTime());
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils par capacité maximale
     */
    public function findByMaxAnimals(int $maxAnimals): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.isVisible = :visible')
           ->andWhere('f.maxAnimals >= :maxAnimals')
           ->setParameter('visible', true)
           ->setParameter('maxAnimals', $maxAnimals);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils par code postal
     */
    public function findByPostalCode(string $postalCode): array
    {
        return $this->findBy(['postalCode' => $postalCode, 'isVisible' => true]);
    }

    /**
     * Trouve les profils par utilisateur
     */
    public function findByUser($user): ?FosterProfile
    {
        return $this->findOneBy(['user' => $user]);
    }

    /**
     * Trouve les profils avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.isVisible = :visible')
           ->setParameter('visible', true);

        if (isset($criteria['region'])) {
            $qb->andWhere('f.region = :region')
               ->setParameter('region', $criteria['region']);
        }

        if (isset($criteria['city'])) {
            $qb->andWhere('f.city = :city')
               ->setParameter('city', $criteria['city']);
        }

        if (isset($criteria['species'])) {
            $qb->andWhere('JSON_CONTAINS(f.speciesAccepted, :species) = 1')
               ->setParameter('species', '"' . $criteria['species'] . '"');
        }

        if (isset($criteria['hasGarden'])) {
            $qb->andWhere('f.hasGarden = :hasGarden')
               ->setParameter('hasGarden', $criteria['hasGarden']);
        }

        if (isset($criteria['childrenAtHome'])) {
            $qb->andWhere('f.childrenAtHome = :childrenAtHome')
               ->setParameter('childrenAtHome', $criteria['childrenAtHome']);
        }

        if (isset($criteria['otherPets'])) {
            $qb->andWhere('f.otherPets = :otherPets')
               ->setParameter('otherPets', $criteria['otherPets']);
        }

        if (isset($criteria['maxAnimals'])) {
            $qb->andWhere('f.maxAnimals >= :maxAnimals')
               ->setParameter('maxAnimals', $criteria['maxAnimals']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les familles d'accueil avec filtres et pagination
     */
    public function findByFilters(?string $region = null, ?string $species = null, int $page = 1, int $limit = 12): array
    {
        $qb = $this->createQueryBuilder('f')
            ->where('f.isVisible = :visible')
            ->setParameter('visible', true)
            ->orderBy('f.createdAt', 'DESC');

        if ($region) {
            $qb->andWhere('f.region = :region')
               ->setParameter('region', $region);
        }

        if ($species) {
            $qb->andWhere('JSON_CONTAINS(f.speciesAccepted, :species) = 1')
               ->setParameter('species', '"' . $species . '"');
        }

        $offset = ($page - 1) * $limit;
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve toutes les régions distinctes des familles d'accueil visibles
     */
    public function findAllRegions(): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('DISTINCT f.region')
            ->where('f.isVisible = :visible')
            ->andWhere('f.region IS NOT NULL')
            ->andWhere('f.region != :empty')
            ->setParameter('visible', true)
            ->setParameter('empty', '')
            ->orderBy('f.region', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return array_column($result, 'region');
    }

    /**
     * Trouve toutes les espèces acceptées distinctes des familles d'accueil visibles
     */
    public function findAllSpecies(): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.speciesAccepted')
            ->where('f.isVisible = :visible')
            ->andWhere('f.speciesAccepted IS NOT NULL')
            ->setParameter('visible', true);

        $result = $qb->getQuery()->getScalarResult();
        $species = [];

        foreach ($result as $row) {
            if ($row['speciesAccepted']) {
                $decoded = json_decode($row['speciesAccepted'], true);
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
