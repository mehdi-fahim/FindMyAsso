<?php

namespace App\Entity;

use App\Repository\ShelterCapacityRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShelterCapacityRepository::class)]
class ShelterCapacity
{
    public const SPECIES_CAT = 'CAT';
    public const SPECIES_DOG = 'DOG';
    public const SPECIES_OTHER = 'OTHER';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'shelterCapacities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Association $association = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::SPECIES_CAT, self::SPECIES_DOG, self::SPECIES_OTHER])]
    private ?string $species = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?int $capacityTotal = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?int $capacityAvailable = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        $this->association = $association;
        return $this;
    }

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(string $species): static
    {
        $this->species = $species;
        return $this;
    }

    public function getCapacityTotal(): ?int
    {
        return $this->capacityTotal;
    }

    public function setCapacityTotal(int $capacityTotal): static
    {
        $this->capacityTotal = $capacityTotal;
        return $this;
    }

    public function getCapacityAvailable(): ?int
    {
        return $this->capacityAvailable;
    }

    public function setCapacityAvailable(int $capacityAvailable): static
    {
        $this->capacityAvailable = $capacityAvailable;
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

    public function getCapacityUsed(): int
    {
        return $this->capacityTotal - $this->capacityAvailable;
    }

    public function getCapacityPercentage(): float
    {
        if ($this->capacityTotal === 0) {
            return 0;
        }
        return round(($this->capacityUsed / $this->capacityTotal) * 100, 1);
    }

    public function getSpeciesLabel(): string
    {
        return match($this->species) {
            self::SPECIES_CAT => 'Chats',
            self::SPECIES_DOG => 'Chiens',
            self::SPECIES_OTHER => 'Autres',
            default => 'Inconnu'
        };
    }

    public function hasAvailableCapacity(): bool
    {
        return $this->capacityAvailable > 0;
    }
}
