<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    public const SPECIES_CAT = 'CAT';
    public const SPECIES_DOG = 'DOG';
    public const SPECIES_OTHER = 'OTHER';

    public const SEX_MALE = 'MALE';
    public const SEX_FEMALE = 'FEMALE';

    public const SIZE_SMALL = 'SMALL';
    public const SIZE_MEDIUM = 'MEDIUM';
    public const SIZE_LARGE = 'LARGE';

    public const STATUS_AVAILABLE = 'AVAILABLE';
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_ADOPTED = 'ADOPTED';
    public const STATUS_WITHDRAWN = 'WITHDRAWN';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Association $association = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::SPECIES_CAT, self::SPECIES_DOG, self::SPECIES_OTHER])]
    private ?string $species = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::SEX_MALE, self::SEX_FEMALE])]
    private ?string $sex = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::SIZE_SMALL, self::SIZE_MEDIUM, self::SIZE_LARGE])]
    private ?string $size = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $color = null;

    #[ORM\Column]
    private ?bool $sterilized = false;

    #[ORM\Column]
    private ?bool $vaccinated = false;

    #[ORM\Column]
    private ?bool $identified = false;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::STATUS_AVAILABLE, self::STATUS_PENDING, self::STATUS_ADOPTED, self::STATUS_WITHDRAWN])]
    private ?string $status = self::STATUS_AVAILABLE;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: AnimalPhoto::class, orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['isMain' => 'DESC', 'sortIndex' => 'ASC'])]
    private Collection $photos;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: AdoptionRequest::class, orphanRemoval: true)]
    private Collection $adoptionRequests;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->adoptionRequests = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
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

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): static
    {
        $this->sex = $sex;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function isSterilized(): ?bool
    {
        return $this->sterilized;
    }

    public function setSterilized(bool $sterilized): static
    {
        $this->sterilized = $sterilized;
        return $this;
    }

    public function isVaccinated(): ?bool
    {
        return $this->vaccinated;
    }

    public function setVaccinated(bool $vaccinated): static
    {
        $this->vaccinated = $vaccinated;
        return $this;
    }

    public function isIdentified(): ?bool
    {
        return $this->identified;
    }

    public function setIdentified(bool $identified): static
    {
        $this->identified = $identified;
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

    /**
     * @return Collection<int, AnimalPhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(AnimalPhoto $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setAnimal($this);
        }
        return $this;
    }

    public function removePhoto(AnimalPhoto $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getAnimal() === $this) {
                $photo->setAnimal(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, AdoptionRequest>
     */
    public function getAdoptionRequests(): Collection
    {
        return $this->adoptionRequests;
    }

    public function addAdoptionRequest(AdoptionRequest $adoptionRequest): static
    {
        if (!$this->adoptionRequests->contains($adoptionRequest)) {
            $this->adoptionRequests->add($adoptionRequest);
            $adoptionRequest->setAnimal($this);
        }
        return $this;
    }

    public function removeAdoptionRequest(AdoptionRequest $adoptionRequest): static
    {
        if ($this->adoptionRequests->removeElement($adoptionRequest)) {
            if ($adoptionRequest->getAnimal() === $this) {
                $adoptionRequest->setAnimal(null);
            }
        }
        return $this;
    }

    public function getMainPhoto(): ?AnimalPhoto
    {
        foreach ($this->photos as $photo) {
            if ($photo->isMain()) {
                return $photo;
            }
        }
        return $this->photos->first() ?: null;
    }

    public function getAge(): ?int
    {
        if (!$this->birthDate) {
            return null;
        }

        $now = new \DateTime();
        $interval = $now->diff($this->birthDate);
        return $interval->y;
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function getSpeciesLabel(): string
    {
        return match($this->species) {
            self::SPECIES_CAT => 'Chat',
            self::SPECIES_DOG => 'Chien',
            self::SPECIES_OTHER => 'Autre',
            default => 'Inconnu'
        };
    }

    public function getSexLabel(): string
    {
        return match($this->sex) {
            self::SEX_MALE => 'Mâle',
            self::SEX_FEMALE => 'Femelle',
            default => 'Inconnu'
        };
    }

    public function getSizeLabel(): string
    {
        return match($this->size) {
            self::SIZE_SMALL => 'Petit',
            self::SIZE_MEDIUM => 'Moyen',
            self::SIZE_LARGE => 'Grand',
            default => 'Inconnu'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'Disponible',
            self::STATUS_PENDING => 'En attente',
            self::STATUS_ADOPTED => 'Adopté',
            self::STATUS_WITHDRAWN => 'Retiré',
            default => 'Inconnu'
        };
    }
}
