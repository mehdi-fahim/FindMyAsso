<?php

namespace App\Repository;

use App\Entity\VetProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VetProfile>
 *
 * @method VetProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method VetProfile[]    findAll()
 * @method VetProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method VetProfile|null findOneBy(array $criteria, array $orderBy = null)
 */
class VetProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VetProfile::class);
    }

    public function save(VetProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VetProfile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les profils approuvés
     */
    public function findApproved(): array
    {
        return $this->findBy(['isApproved' => true]);
    }

    /**
     * Trouve les profils en attente d'approbation
     */
    public function findPendingApproval(): array
    {
        return $this->findBy(['isApproved' => false]);
    }

    /**
     * Trouve les profils par région
     */
    public function findByRegion(string $region): array
    {
        return $this->findBy(['region' => $region, 'isApproved' => true]);
    }

    /**
     * Trouve les profils par département
     */
    public function findByDepartment(string $department): array
    {
        return $this->findBy(['department' => $department, 'isApproved' => true]);
    }

    /**
     * Trouve les profils par ville
     */
    public function findByCity(string $city): array
    {
        return $this->findBy(['city' => $city, 'isApproved' => true]);
    }

    /**
     * Trouve les profils par code postal
     */
    public function findByPostalCode(string $postalCode): array
    {
        return $this->findBy(['postalCode' => $postalCode, 'isApproved' => true]);
    }

    /**
     * Trouve les profils dans un rayon donné
     */
    public function findByRadius(float $lat, float $lng, float $radiusKm): array
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where('v.isApproved = :approved')
           ->andWhere('v.lat IS NOT NULL')
           ->andWhere('v.lng IS NOT NULL')
           ->andWhere('(
               (6371 * acos(cos(radians(:lat)) * cos(radians(v.lat)) * 
                cos(radians(v.lng) - radians(:lng)) + sin(radians(:lat)) * 
                sin(radians(v.lat)))) <= :radius
           )')
           ->setParameter('approved', true)
           ->setParameter('lat', $lat)
           ->setParameter('lng', $lng)
           ->setParameter('radius', $radiusKm);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils avec des créneaux disponibles
     */
    public function findWithAvailableSlots(): array
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where('v.isApproved = :approved')
           ->andWhere('v.freeCareSlots > 0')
           ->setParameter('approved', true);
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils par service
     */
    public function findByService(string $service): array
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where('v.isApproved = :approved')
           ->andWhere('JSON_CONTAINS(v.services, :service) = 1')
           ->setParameter('approved', true)
           ->setParameter('service', '"' . $service . '"');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils par nom de clinique
     */
    public function findByClinicName(string $clinicName): array
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where('v.isApproved = :approved')
           ->andWhere('v.clinicName LIKE :clinicName')
           ->setParameter('approved', true)
           ->setParameter('clinicName', '%' . $clinicName . '%');
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les profils par utilisateur
     */
    public function findByUser($user): ?VetProfile
    {
        return $this->findOneBy(['user' => $user]);
    }

    /**
     * Trouve les profils avec des critères multiples
     */
    public function findByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('v');
        $qb->where('v.isApproved = :approved')
           ->setParameter('approved', true);

        if (isset($criteria['region'])) {
            $qb->andWhere('v.region = :region')
               ->setParameter('region', $criteria['region']);
        }

        if (isset($criteria['city'])) {
            $qb->andWhere('v.city = :city')
               ->setParameter('city', $criteria['city']);
        }

        if (isset($criteria['department'])) {
            $qb->andWhere('v.department = :department')
               ->setParameter('department', $criteria['department']);
        }

        if (isset($criteria['service'])) {
            $qb->andWhere('JSON_CONTAINS(v.services, :service) = 1')
               ->setParameter('service', '"' . $criteria['service'] . '"');
        }

        if (isset($criteria['hasAvailableSlots'])) {
            if ($criteria['hasAvailableSlots']) {
                $qb->andWhere('v.freeCareSlots > 0');
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les vétérinaires avec filtres et pagination
     */
    public function findByFilters(?string $region = null, ?string $service = null, int $page = 1, int $limit = 12): array
    {
        $qb = $this->createQueryBuilder('v')
            ->where('v.isApproved = :approved')
            ->setParameter('approved', true)
            ->orderBy('v.clinicName', 'ASC');

        if ($region) {
            $qb->andWhere('v.region = :region')
               ->setParameter('region', $region);
        }

        if ($service) {
            $qb->andWhere('JSON_CONTAINS(v.services, :service) = 1')
               ->setParameter('service', '"' . $service . '"');
        }

        $offset = ($page - 1) * $limit;
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve toutes les régions distinctes des vétérinaires approuvés
     */
    public function findAllRegions(): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('DISTINCT v.region')
            ->where('v.isApproved = :approved')
            ->andWhere('v.region IS NOT NULL')
            ->andWhere('v.region != :empty')
            ->setParameter('approved', true)
            ->setParameter('empty', '')
            ->orderBy('v.region', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return array_column($result, 'region');
    }

    /**
     * Trouve tous les services distincts des vétérinaires approuvés
     */
    public function findAllServices(): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('v.services')
            ->where('v.isApproved = :approved')
            ->andWhere('v.services IS NOT NULL')
            ->setParameter('approved', true);

        $result = $qb->getQuery()->getScalarResult();
        $services = [];

        foreach ($result as $row) {
            if ($row['services']) {
                $decoded = json_decode($row['services'], true);
                if (is_array($decoded)) {
                    $services = array_merge($services, $decoded);
                }
            }
        }

        $services = array_unique($services);
        sort($services);
        return $services;
    }
}
