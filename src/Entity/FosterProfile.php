<?php

namespace App\Entity;

use App\Repository\FosterProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FosterProfileRepository::class)]
class FosterProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\OneToOne(inversedBy: 'fosterProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'json')]
    private array $speciesAccepted = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?int $maxAnimals = null;

    #[ORM\Column]
    private ?bool $hasGarden = false;

    #[ORM\Column]
    private ?bool $childrenAtHome = false;

    #[ORM\Column]
    private ?bool $otherPets = false;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $availabilityFrom = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $availabilityTo = null;

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

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?bool $isVisible = true;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $questionnaireAnswers = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $housingType = null;

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

    public function getSpeciesAccepted(): array
    {
        return $this->speciesAccepted;
    }

    public function setSpeciesAccepted(array $speciesAccepted): static
    {
        $this->speciesAccepted = $speciesAccepted;
        return $this;
    }

    public function getMaxAnimals(): ?int
    {
        return $this->maxAnimals;
    }

    public function setMaxAnimals(int $maxAnimals): static
    {
        $this->maxAnimals = $maxAnimals;
        return $this;
    }

    public function isHasGarden(): ?bool
    {
        return $this->hasGarden;
    }

    public function setHasGarden(bool $hasGarden): static
    {
        $this->hasGarden = $hasGarden;
        return $this;
    }

    public function isChildrenAtHome(): ?bool
    {
        return $this->childrenAtHome;
    }

    public function setChildrenAtHome(bool $childrenAtHome): static
    {
        $this->childrenAtHome = $childrenAtHome;
        return $this;
    }

    public function isOtherPets(): ?bool
    {
        return $this->otherPets;
    }

    public function setOtherPets(bool $otherPets): static
    {
        $this->otherPets = $otherPets;
        return $this;
    }

    public function getAvailabilityFrom(): ?\DateTimeInterface
    {
        return $this->availabilityFrom;
    }

    public function setAvailabilityFrom(?\DateTimeInterface $availabilityFrom): static
    {
        $this->availabilityFrom = $availabilityFrom;
        return $this;
    }

    public function getAvailabilityTo(): ?\DateTimeInterface
    {
        return $this->availabilityTo;
    }

    public function setAvailabilityTo(?\DateTimeInterface $availabilityTo): static
    {
        $this->availabilityTo = $availabilityTo;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;
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

    public function isAvailable(): bool
    {
        if (!$this->availabilityFrom || !$this->availabilityTo) {
            return true; // Si pas de dates, considéré comme toujours disponible
        }

        $now = new \DateTime();
        return $now >= $this->availabilityFrom && $now <= $this->availabilityTo;
    }

    public function getSpeciesAcceptedLabels(): array
    {
        $labels = [];
        foreach ($this->speciesAccepted as $species) {
            $labels[] = match($species) {
                'CAT' => 'Chats',
                'DOG' => 'Chiens',
                'OTHER' => 'Autres',
                default => 'Inconnu'
            };
        }
        return $labels;
    }

    public function getQuestionnaireAnswers(): ?array
    {
        return $this->questionnaireAnswers;
    }

    public function setQuestionnaireAnswers(?array $questionnaireAnswers): static
    {
        $this->questionnaireAnswers = $questionnaireAnswers;
        return $this;
    }

    public function getHousingType(): ?string
    {
        return $this->housingType;
    }

    public function setHousingType(string $housingType): static
    {
        $this->housingType = $housingType;
        return $this;
    }

    public function getHousingTypeLabel(): string
    {
        return match($this->housingType) {
            'APARTMENT' => 'Appartement',
            'HOUSE' => 'Maison',
            'FARM' => 'Ferme',
            'OTHER' => 'Autre',
            default => 'Non spécifié'
        };
    }

    public function getCapacity(): int
    {
        return $this->maxAnimals ?? 0;
    }

    public function setCapacity(int $capacity): static
    {
        $this->maxAnimals = $capacity;
        return $this;
    }
}
