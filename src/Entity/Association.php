<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\OneToOne(inversedBy: 'association', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[Assert\Length(exactly: 14)]
    private ?string $siret = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $emailPublic = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $phonePublic = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $website = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    private ?string $description = null;

    #[ORM\Column(type: 'json')]
    private array $speciesSupported = [];

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

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banner = null;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: ShelterCapacity::class, orphanRemoval: true)]
    private Collection $shelterCapacities;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Animal::class, orphanRemoval: true)]
    private Collection $animals;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: WishlistItem::class, orphanRemoval: true)]
    private Collection $wishlistItems;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Donation::class)]
    private Collection $donations;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: InKindDonation::class)]
    private Collection $inKindDonations;

    public function __construct()
    {
        $this->shelterCapacities = new ArrayCollection();
        $this->animals = new ArrayCollection();
        $this->wishlistItems = new ArrayCollection();
        $this->donations = new ArrayCollection();
        $this->inKindDonations = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;
        return $this;
    }

    public function getEmailPublic(): ?string
    {
        return $this->emailPublic;
    }

    public function setEmailPublic(string $emailPublic): static
    {
        $this->emailPublic = $emailPublic;
        return $this;
    }

    public function getPhonePublic(): ?string
    {
        return $this->phonePublic;
    }

    public function setPhonePublic(?string $phonePublic): static
    {
        $this->phonePublic = $phonePublic;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;
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

    public function getSpeciesSupported(): array
    {
        return $this->speciesSupported;
    }

    public function setSpeciesSupported(array $speciesSupported): static
    {
        $this->speciesSupported = $speciesSupported;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;
        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): static
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * @return Collection<int, ShelterCapacity>
     */
    public function getShelterCapacities(): Collection
    {
        return $this->shelterCapacities;
    }

    public function addShelterCapacity(ShelterCapacity $shelterCapacity): static
    {
        if (!$this->shelterCapacities->contains($shelterCapacity)) {
            $this->shelterCapacities->add($shelterCapacity);
            $shelterCapacity->setAssociation($this);
        }
        return $this;
    }

    public function removeShelterCapacity(ShelterCapacity $shelterCapacity): static
    {
        if ($this->shelterCapacities->removeElement($shelterCapacity)) {
            if ($shelterCapacity->getAssociation() === $this) {
                $shelterCapacity->setAssociation(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setAssociation($this);
        }
        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            if ($animal->getAssociation() === $this) {
                $animal->setAssociation(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, WishlistItem>
     */
    public function getWishlistItems(): Collection
    {
        return $this->wishlistItems;
    }

    public function addWishlistItem(WishlistItem $wishlistItem): static
    {
        if (!$this->wishlistItems->contains($wishlistItem)) {
            $this->wishlistItems->add($wishlistItem);
            $wishlistItem->setAssociation($this);
        }
        return $this;
    }

    public function removeWishlistItem(WishlistItem $wishlistItem): static
    {
        if ($this->wishlistItems->removeElement($wishlistItem)) {
            if ($wishlistItem->getAssociation() === $this) {
                $wishlistItem->setAssociation(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Donation>
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): static
    {
        if (!$this->donations->contains($donation)) {
            $this->donations->add($donation);
            $donation->setAssociation($this);
        }
        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            if ($donation->getAssociation() === $this) {
                $donation->setAssociation(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, InKindDonation>
     */
    public function getInKindDonations(): Collection
    {
        return $this->inKindDonations;
    }

    public function addInKindDonation(InKindDonation $inKindDonation): static
    {
        if (!$this->inKindDonations->contains($inKindDonation)) {
            $this->inKindDonations->add($inKindDonation);
            $inKindDonation->setAssociation($this);
        }
        return $this;
    }

    public function removeInKindDonation(InKindDonation $inKindDonation): static
    {
        if ($this->inKindDonations->removeElement($inKindDonation)) {
            if ($inKindDonation->getAssociation() === $this) {
                $inKindDonation->setAssociation(null);
            }
        }
        return $this;
    }

    public function getFullAddress(): string
    {
        return sprintf('%s, %s %s, %s', $this->street, $this->postalCode, $this->city, $this->region);
    }

    public function getAvailableCapacityForSpecies(string $species): int
    {
        foreach ($this->shelterCapacities as $capacity) {
            if ($capacity->getSpecies() === $species) {
                return $capacity->getCapacityAvailable();
            }
        }
        return 0;
    }
}
