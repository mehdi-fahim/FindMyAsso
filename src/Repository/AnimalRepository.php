<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Animal>
 *
 * @method Animal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animal[]    findAll()
 * @method Animal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    public function save(Animal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Animal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les animaux disponibles
     */
    public function findAvailable(): array
    {
        return $this->findBy(['status' => Animal::STATUS_AVAILABLE]);
    }

    /**
     * Trouve les animaux par espèce
     */
    public function findBySpecies(string $species): array
    {
        return $this->findBy(['species' => $species, 'status' => Animal::STATUS_AVAILABLE]);
    }

    /**
     * Trouve les animaux par espèce et taille
     */
    public function findBySpeciesAndSize(string $species, string $size): array
    {
        return $this->findBy([
            'species' => $species,
            'size' => $size,
            'status' => Animal::STATUS_AVAILABLE
        ]);
    }

    /**
     * Trouve les animaux par espèce et sexe
     */
    public function findBySpeciesAndSex(string $species, string $sex): array
    {
        return $this->findBy([
            'species' => $species,
            'sex' => $sex,
            'status' => Animal::STATUS_AVAILABLE
        ]);
    }

    /**
     * Trouve les animaux par association
     */
    public function findByAssociation($association): array
    {
        return $this->findBy(['association' => $association]);
    }

    /**
     * Trouve les animaux par association et statut
     */
    public function findByAssociationAndStatus($association, string $status): array
    {
        return $this->findBy(['association' => $association, 'status' => $status]);
    }

    /**
     * Trouve les animaux par région
     */
    public function findByRegion(string $region): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.association', 'assoc')
           ->where('a.status = :status')
           ->andWhere('assoc.region = :region')
           ->andWhere('assoc.isApproved = :approved')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('region', $region)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux par ville
     */
    public function findByCity(string $city): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.association', 'assoc')
           ->where('a.status = :status')
           ->andWhere('assoc.city = :city')
           ->andWhere('assoc.isApproved = :approved')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('city', $city)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux par département
     */
    public function findByDepartment(string $department): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.association', 'assoc')
           ->where('a.status = :status')
           ->andWhere('assoc.department = :department')
           ->andWhere('assoc.isApproved = :approved')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('department', $department)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux par code postal
     */
    public function findByPostalCode(string $postalCode): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.association', 'assoc')
           ->where('a.status = :status')
           ->andWhere('assoc.postalCode = :postalCode')
           ->andWhere('assoc.isApproved = :approved')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('postalCode', $postalCode)
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux dans un rayon donné
     */
    public function findByRadius(float $lat, float $lng, float $radiusKm): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.association', 'assoc')
           ->where('a.status = :status')
           ->andWhere('assoc.isApproved = :approved')
           ->andWhere('assoc.lat IS NOT NULL')
           ->andWhere('assoc.lng IS NOT NULL')
           ->andWhere('(
               (6371 * acos(cos(radians(:lat)) * cos(radians(assoc.lat)) * 
                cos(radians(assoc.lng) - radians(:lng)) + sin(radians(:lat)) * 
                sin(radians(assoc.lat)))) <= :radius
           )')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('approved', true)
           ->setParameter('lat', $lat)
           ->setParameter('lng', $lng)
           ->setParameter('radius', $radiusKm);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux stérilisés
     */
    public function findSterilized(): array
    {
        return $this->findBy(['sterilized' => true, 'status' => Animal::STATUS_AVAILABLE]);
    }

    /**
     * Trouve les animaux vaccinés
     */
    public function findVaccinated(): array
    {
        return $this->findBy(['vaccinated' => true, 'status' => Animal::STATUS_AVAILABLE]);
    }

    /**
     * Trouve les animaux identifiés
     */
    public function findIdentified(): array
    {
        return $this->findBy(['identified' => true, 'status' => Animal::STATUS_AVAILABLE]);
    }

    /**
     * Trouve les animaux par âge approximatif
     */
    public function findByAgeRange(int $minAge, int $maxAge): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.status = :status')
           ->andWhere('a.birthDate IS NOT NULL')
           ->andWhere('a.birthDate <= :maxDate')
           ->andWhere('a.birthDate >= :minDate')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('maxDate', new \DateTime("-{$minAge} years"))
           ->setParameter('minDate', new \DateTime("-{$maxAge} years"));
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux par nom (recherche partielle)
     */
    public function findByNameLike(string $name): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.status = :status')
           ->andWhere('a.name LIKE :name')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('name', '%' . $name . '%');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux récents
     */
    public function findRecent(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.status = :status')
           ->orderBy('a.createdAt', 'DESC')
           ->setMaxResults($limit)
           ->setParameter('status', Animal::STATUS_AVAILABLE);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux par couleur
     */
    public function findByColor(string $color): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.status = :status')
           ->andWhere('a.color LIKE :color')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('color', '%' . $color . '%');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.association', 'assoc')
           ->where('a.status = :status')
           ->andWhere('assoc.isApproved = :approved')
           ->setParameter('status', Animal::STATUS_AVAILABLE)
           ->setParameter('approved', true);

        if (isset($criteria['species'])) {
            $qb->andWhere('a.species = :species')
               ->setParameter('species', $criteria['species']);
        }

        if (isset($criteria['size'])) {
            $qb->andWhere('a.size = :size')
               ->setParameter('size', $criteria['size']);
        }

        if (isset($criteria['sex'])) {
            $qb->andWhere('a.sex = :sex')
               ->setParameter('sex', $criteria['sex']);
        }

        if (isset($criteria['sterilized'])) {
            $qb->andWhere('a.sterilized = :sterilized')
               ->setParameter('sterilized', $criteria['sterilized']);
        }

        if (isset($criteria['vaccinated'])) {
            $qb->andWhere('a.vaccinated = :vaccinated')
               ->setParameter('vaccinated', $criteria['vaccinated']);
        }

        if (isset($criteria['region'])) {
            $qb->andWhere('assoc.region = :region')
               ->setParameter('region', $criteria['region']);
        }

        if (isset($criteria['city'])) {
            $qb->andWhere('assoc.city = :city')
               ->setParameter('city', $criteria['city']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les animaux avec filtres et pagination
     */
    public function findByFilters(?string $species = null, ?string $size = null, ?string $sex = null, ?string $region = null, int $page = 1, int $limit = 12): array
    {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.association', 'assoc')
            ->where('a.status = :status')
            ->andWhere('assoc.isApproved = :approved')
            ->setParameter('status', Animal::STATUS_AVAILABLE)
            ->setParameter('approved', true)
            ->orderBy('a.createdAt', 'DESC');

        if ($species) {
            $qb->andWhere('a.species = :species')
               ->setParameter('species', $species);
        }

        if ($size) {
            $qb->andWhere('a.size = :size')
               ->setParameter('size', $size);
        }

        if ($sex) {
            $qb->andWhere('a.sex = :sex')
               ->setParameter('sex', $sex);
        }

        if ($region) {
            $qb->andWhere('assoc.region = :region')
               ->setParameter('region', $region);
        }

        $offset = ($page - 1) * $limit;
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve toutes les espèces distinctes des animaux disponibles
     */
    public function findAllSpecies(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.species')
            ->where('a.status = :status')
            ->andWhere('a.species IS NOT NULL')
            ->andWhere('a.species != :empty')
            ->setParameter('status', Animal::STATUS_AVAILABLE)
            ->setParameter('empty', '')
            ->orderBy('a.species', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return array_column($result, 'species');
    }

    /**
     * Trouve toutes les tailles distinctes des animaux disponibles
     */
    public function findAllSizes(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.size')
            ->where('a.status = :status')
            ->andWhere('a.size IS NOT NULL')
            ->andWhere('a.size != :empty')
            ->setParameter('status', Animal::STATUS_AVAILABLE)
            ->setParameter('empty', '')
            ->orderBy('a.size', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return array_column($result, 'size');
    }

    /**
     * Trouve tous les sexes distincts des animaux disponibles
     */
    public function findAllSexes(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.sex')
            ->where('a.status = :status')
            ->andWhere('a.sex IS NOT NULL')
            ->andWhere('a.sex != :empty')
            ->setParameter('status', Animal::STATUS_AVAILABLE)
            ->setParameter('empty', '')
            ->orderBy('a.sex', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return array_column($result, 'sex');
    }

    /**
     * Trouve toutes les régions distinctes des associations avec animaux disponibles
     */
    public function findAllRegions(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT assoc.region')
            ->innerJoin('a.association', 'assoc')
            ->where('a.status = :status')
            ->andWhere('assoc.isApproved = :approved')
            ->andWhere('assoc.region IS NOT NULL')
            ->andWhere('assoc.region != :empty')
            ->setParameter('status', Animal::STATUS_AVAILABLE)
            ->setParameter('approved', true)
            ->setParameter('empty', '')
            ->orderBy('assoc.region', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return array_column($result, 'region');
    }
}
