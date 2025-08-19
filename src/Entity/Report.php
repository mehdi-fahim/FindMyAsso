<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    public const TARGET_TYPE_ANIMAL = 'ANIMAL';
    public const TARGET_TYPE_ASSOCIATION = 'ASSOCIATION';
    public const TARGET_TYPE_USER = 'USER';

    public const STATUS_OPEN = 'OPEN';
    public const STATUS_REVIEWING = 'REVIEWING';
    public const STATUS_CLOSED = 'CLOSED';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reporter = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TARGET_TYPE_ANIMAL, self::TARGET_TYPE_ASSOCIATION, self::TARGET_TYPE_USER])]
    private ?string $targetType = null;

    #[ORM\Column(type: 'uuid')]
    #[Assert\NotBlank]
    private ?string $targetId = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::STATUS_OPEN, self::STATUS_REVIEWING, self::STATUS_CLOSED])]
    private ?string $status = self::STATUS_OPEN;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adminNotes = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminAction = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(?User $reporter): static
    {
        $this->reporter = $reporter;
        return $this;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function setTargetType(string $targetType): static
    {
        $this->targetType = $targetType;
        return $this;
    }

    public function getTargetId(): ?string
    {
        return $this->targetId;
    }

    public function setTargetId(string $targetId): static
    {
        $this->targetId = $targetId;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getAdminNotes(): ?string
    {
        return $this->adminNotes;
    }

    public function setAdminNotes(?string $adminNotes): static
    {
        $this->adminNotes = $adminNotes;
        return $this;
    }

    public function getReviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }

    public function setReviewedAt(?\DateTimeImmutable $reviewedAt): static
    {
        $this->reviewedAt = $reviewedAt;
        return $this;
    }

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): static
    {
        $this->closedAt = $closedAt;
        return $this;
    }

    public function getAdminAction(): ?string
    {
        return $this->adminAction;
    }

    public function setAdminAction(?string $adminAction): static
    {
        $this->adminAction = $adminAction;
        return $this;
    }

    public function getTargetTypeLabel(): string
    {
        return match($this->targetType) {
            self::TARGET_TYPE_ANIMAL => 'Animal',
            self::TARGET_TYPE_ASSOCIATION => 'Association',
            self::TARGET_TYPE_USER => 'Utilisateur',
            default => 'Inconnu'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'Ouvert',
            self::STATUS_REVIEWING => 'En cours d\'examen',
            self::STATUS_CLOSED => 'Fermé',
            default => 'Inconnu'
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_OPEN => 'red',
            self::STATUS_REVIEWING => 'yellow',
            self::STATUS_CLOSED => 'green',
            default => 'gray'
        };
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isUnderReview(): bool
    {
        return $this->status === self::STATUS_REVIEWING;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function canBeClosed(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_REVIEWING]);
    }

    public function getAgeInDays(): int
    {
        $now = new \DateTimeImmutable();
        $interval = $now->diff($this->createdAt);
        return $interval->days;
    }

    public function isUrgent(): bool
    {
        return $this->getAgeInDays() > 7; // Considéré urgent après 7 jours
    }
}
