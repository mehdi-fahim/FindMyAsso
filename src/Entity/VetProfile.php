<?php

namespace App\Entity;

use App\Repository\VetProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VetProfileRepository::class)]
class VetProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\OneToOne(inversedBy: 'vetProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $clinicName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $rppsOrLicense = null;

    #[ORM\Column(type: 'json')]
    private array $services = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?int $freeCareSlots = 0;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $region = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $department = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^\d{5}$/')]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $street = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 8, nullable: true)]
    private ?string $lat = null;

    #[ORM\Column(type: 'decimal', precision: 11, scale: 8, nullable: true)]
    private ?string $lng = null;

    #[ORM\Column]
    private ?bool $isApproved = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getClinicName(): ?string
    {
        return $this->clinicName;
    }

    public function setClinicName(string $clinicName): static
    {
        $this->clinicName = $clinicName;
        return $this;
    }

    public function getRppsOrLicense(): ?string
    {
        return $this->rppsOrLicense;
    }

    public function setRppsOrLicense(string $rppsOrLicense): static
    {
        $this->rppsOrLicense = $rppsOrLicense;
        return $this;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function setServices(array $services): static
    {
        $this->services = $services;
        return $this;
    }

    public function getFreeCareSlots(): ?int
    {
        return $this->freeCareSlots;
    }

    public function setFreeCareSlots(int $freeCareSlots): static
    {
        $this->freeCareSlots = $freeCareSlots;
        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;
        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): static
    {
        $this->department = $department;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;
        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): static
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): static
    {
        $this->lng = $lng;
        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getFullAddress(): string
    {
        return sprintf('%s, %s %s, %s', $this->street, $this->postalCode, $this->city, $this->region);
    }

    public function hasAvailableSlots(): bool
    {
        return $this->freeCareSlots > 0;
    }

    public function getServicesLabels(): array
    {
        $labels = [];
        foreach ($this->services as $service) {
            $labels[] = $service; // Les services sont des chaînes libres
        }
        return $labels;
    }
}
