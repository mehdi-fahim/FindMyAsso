<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?string $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Association $association = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?FosterProfile $fosterProfile = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?VetProfile $vetProfile = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Donation::class)]
    private Collection $donations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: InKindDonation::class)]
    private Collection $inKindDonations;

    #[ORM\OneToMany(mappedBy: 'requester', targetEntity: AdoptionRequest::class)]
    private Collection $adoptionRequests;

    #[ORM\OneToMany(mappedBy: 'reporter', targetEntity: Report::class)]
    private Collection $reports;

    public function __construct()
    {
        $this->donations = new ArrayCollection();
        $this->inKindDonations = new ArrayCollection();
        $this->adoptionRequests = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
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

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        if ($association === null && $this->association !== null) {
            $this->association->setOwner(null);
        }

        if ($association !== null && $association->getOwner() !== $this) {
            $association->setOwner($this);
        }

        $this->association = $association;
        return $this;
    }

    public function getFosterProfile(): ?FosterProfile
    {
        return $this->fosterProfile;
    }

    public function setFosterProfile(?FosterProfile $fosterProfile): static
    {
        if ($fosterProfile === null && $this->fosterProfile !== null) {
            $this->fosterProfile->setUser(null);
        }

        if ($fosterProfile !== null && $fosterProfile->getUser() !== $this) {
            $fosterProfile->setUser($this);
        }

        $this->fosterProfile = $fosterProfile;
        return $this;
    }

    public function getVetProfile(): ?VetProfile
    {
        return $this->vetProfile;
    }

    public function setVetProfile(?VetProfile $vetProfile): static
    {
        if ($vetProfile === null && $this->vetProfile !== null) {
            $this->vetProfile->setUser(null);
        }

        if ($vetProfile !== null && $vetProfile->getUser() !== $this) {
            $vetProfile->setUser($this);
        }

        $this->vetProfile = $vetProfile;
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
            $donation->setUser($this);
        }
        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            if ($donation->getUser() === $this) {
                $donation->setUser(null);
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
            $inKindDonation->setUser($this);
        }
        return $this;
    }

    public function removeInKindDonation(InKindDonation $inKindDonation): static
    {
        if ($this->inKindDonations->removeElement($inKindDonation)) {
            if ($inKindDonation->getUser() === $this) {
                $inKindDonation->setUser(null);
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
            $adoptionRequest->setRequester($this);
        }
        return $this;
    }

    public function removeAdoptionRequest(AdoptionRequest $adoptionRequest): static
    {
        if ($this->adoptionRequests->removeElement($adoptionRequest)) {
            if ($adoptionRequest->getRequester() === $this) {
                $adoptionRequest->setRequester(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setReporter($this);
        }
        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            if ($report->getReporter() === $this) {
                $report->setReporter(null);
            }
        }
        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }
}
