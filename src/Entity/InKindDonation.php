<?php

namespace App\Entity;

use App\Repository\InKindDonationRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InKindDonationRepository::class)]
class InKindDonation
{
    public const TYPE_FOOD = 'FOOD';
    public const TYPE_LITTER = 'LITTER';
    public const TYPE_TOYS = 'TOYS';
    public const TYPE_MEDICINE = 'MEDICINE';
    public const TYPE_OTHER = 'OTHER';

    public const STATUS_OFFERED = 'OFFERED';
    public const STATUS_MATCHED = 'MATCHED';
    public const STATUS_DELIVERED = 'DELIVERED';
    public const STATUS_CANCELLED = 'CANCELLED';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'inKindDonations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'inKindDonations')]
    private ?Association $association = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TYPE_FOOD, self::TYPE_LITTER, self::TYPE_TOYS, self::TYPE_MEDICINE, self::TYPE_OTHER])]
    private ?string $type = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $quantity = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::STATUS_OFFERED, self::STATUS_MATCHED, self::STATUS_DELIVERED, self::STATUS_CANCELLED])]
    private ?string $status = self::STATUS_OFFERED;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $region = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $city = null;

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

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        $this->association = $association;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;
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

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_OFFERED => 'Offert',
            self::STATUS_MATCHED => 'Associé',
            self::STATUS_DELIVERED => 'Livré',
            self::STATUS_CANCELLED => 'Annulé',
            default => 'Inconnu'
        };
    }

    public function isGeneralDonation(): bool
    {
        return $this->association === null;
    }

    public function canBeMatched(): bool
    {
        return $this->status === self::STATUS_OFFERED;
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }
}
