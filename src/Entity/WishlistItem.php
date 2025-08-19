<?php

namespace App\Entity;

use App\Repository\WishlistItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishlistItemRepository::class)]
class WishlistItem
{
    public const TYPE_FOOD = 'FOOD';
    public const TYPE_LITTER = 'LITTER';
    public const TYPE_TOYS = 'TOYS';
    public const TYPE_MEDICINE = 'MEDICINE';
    public const TYPE_OTHER = 'OTHER';

    public const URGENCY_LOW = 'LOW';
    public const URGENCY_MEDIUM = 'MEDIUM';
    public const URGENCY_HIGH = 'HIGH';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'wishlistItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Association $association = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TYPE_FOOD, self::TYPE_LITTER, self::TYPE_TOYS, self::TYPE_MEDICINE, self::TYPE_OTHER])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $quantityNeeded = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::URGENCY_LOW, self::URGENCY_MEDIUM, self::URGENCY_HIGH])]
    private ?string $urgency = self::URGENCY_MEDIUM;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getQuantityNeeded(): ?string
    {
        return $this->quantityNeeded;
    }

    public function setQuantityNeeded(string $quantityNeeded): static
    {
        $this->quantityNeeded = $quantityNeeded;
        return $this;
    }

    public function getUrgency(): ?string
    {
        return $this->urgency;
    }

    public function setUrgency(string $urgency): static
    {
        $this->urgency = $urgency;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_FOOD => 'Nourriture',
            self::TYPE_LITTER => 'Litière',
            self::TYPE_TOYS => 'Jouets',
            self::TYPE_MEDICINE => 'Médicaments',
            self::TYPE_OTHER => 'Autre',
            default => 'Inconnu'
        };
    }

    public function getUrgencyLabel(): string
    {
        return match($this->urgency) {
            self::URGENCY_LOW => 'Faible',
            self::URGENCY_MEDIUM => 'Moyenne',
            self::URGENCY_HIGH => 'Élevée',
            default => 'Inconnue'
        };
    }

    public function getUrgencyColor(): string
    {
        return match($this->urgency) {
            self::URGENCY_LOW => 'green',
            self::URGENCY_MEDIUM => 'yellow',
            self::URGENCY_HIGH => 'red',
            default => 'gray'
        };
    }

    public function isUrgent(): bool
    {
        return $this->urgency === self::URGENCY_HIGH;
    }
}
