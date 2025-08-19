<?php

namespace App\DataFixtures;

use App\Entity\AdoptionRequest;
use App\Entity\Animal;
use App\Entity\AnimalPhoto;
use App\Entity\Association;
use App\Entity\Donation;
use App\Entity\FosterProfile;
use App\Entity\InKindDonation;
use App\Entity\Report;
use App\Entity\ShelterCapacity;
use App\Entity\User;
use App\Entity\VetProfile;
use App\Entity\WishlistItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Créer les utilisateurs de base
        $admin = $this->createUser('admin@findmyasso.fr', 'ROLE_ADMIN', 'Admin', 'admin123');
        $manager->persist($admin);
        
        // Créer l'administrateur principal FindMyAsso
        $findMyAssoAdmin = $this->createUser('admin.findmyasso@gmail.com', 'ROLE_ADMIN', 'Administrateur FindMyAsso', 'FindMyAsso93');
        $manager->persist($findMyAssoAdmin);

        // Créer 10 associations avec leurs propriétaires
        $associations = [];
        $associationOwners = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $owner = $this->createUser(
                "asso{$i}@example.com",
                'ROLE_ASSOCIATION',
                "Propriétaire Asso {$i}",
                "password{$i}"
            );
            $manager->persist($owner);
            $associationOwners[] = $owner;

            $association = $this->createAssociation($owner, $i);
            $manager->persist($association);
            $associations[] = $association;

            // Créer les capacités d'accueil pour chaque association
            $this->createShelterCapacities($manager, $association, $i);
        }

        // Créer 20 familles d'accueil
        $fosterProfiles = [];
        for ($i = 1; $i <= 20; $i++) {
            $fosterUser = $this->createUser(
                "foster{$i}@example.com",
                'ROLE_FOSTER',
                "Famille d'accueil {$i}",
                "password{$i}"
            );
            $manager->persist($fosterUser);

            $fosterProfile = $this->createFosterProfile($fosterUser, $i);
            $manager->persist($fosterProfile);
            $fosterProfiles[] = $fosterProfile;
        }

        // Créer 5 vétérinaires
        $vetProfiles = [];
        for ($i = 1; $i <= 5; $i++) {
            $vetUser = $this->createUser(
                "vet{$i}@example.com",
                'ROLE_VET',
                "Dr. Vétérinaire {$i}",
                "password{$i}"
            );
            $manager->persist($vetUser);

            $vetProfile = $this->createVetProfile($vetUser, $i);
            $manager->persist($vetProfile);
            $vetProfiles[] = $vetProfile;
        }

        // Créer 50 animaux
        $animals = [];
        for ($i = 1; $i <= 50; $i++) {
            $association = $associations[array_rand($associations)];
            $animal = $this->createAnimal($association, $i);
            $manager->persist($animal);
            $animals[] = $animal;

            // Créer 1-3 photos pour chaque animal
            $this->createAnimalPhotos($manager, $animal, $i);
        }

        // Créer 30 demandes d'adoption
        for ($i = 1; $i <= 30; $i++) {
            $adopter = $this->createUser(
                "adopter{$i}@example.com",
                'ROLE_ADOPTER',
                "Adoptant {$i}",
                "password{$i}"
            );
            $manager->persist($adopter);

            $animal = $animals[array_rand($animals)];
            $adoptionRequest = $this->createAdoptionRequest($adopter, $animal, $i);
            $manager->persist($adoptionRequest);
        }

        // Créer 30 dons monétaires
        for ($i = 1; $i <= 30; $i++) {
            $donor = $this->createUser(
                "donor{$i}@example.com",
                'ROLE_DONOR',
                "Donateur {$i}",
                "password{$i}"
            );
            $manager->persist($donor);

            $donation = $this->createDonation($donor, $associations[array_rand($associations)], $i);
            $manager->persist($donation);
        }

        // Créer 20 dons en nature
        for ($i = 1; $i <= 20; $i++) {
            $donor = $this->createUser(
                "donor_nature{$i}@example.com",
                'ROLE_DONOR',
                "Donateur Nature {$i}",
                "password{$i}"
            );
            $manager->persist($donor);

            $donation = $this->createInKindDonation($donor, $associations[array_rand($associations)], $i);
            $manager->persist($donation);
        }

        // Créer des éléments de wishlist pour les associations
        foreach ($associations as $association) {
            $this->createWishlistItems($manager, $association);
        }

        // Créer quelques signalements
        for ($i = 1; $i <= 10; $i++) {
            $reporter = $this->createUser(
                "reporter{$i}@example.com",
                'ROLE_ADOPTER',
                "Signaleur {$i}",
                "password{$i}"
            );
            $manager->persist($reporter);

            $report = $this->createReport($reporter, $i);
            $manager->persist($report);
        }

        $manager->flush();
    }

    private function createUser(string $email, string $role, string $fullName, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setFullName($fullName);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setIsVerified(true);
        $user->setCreatedAt(new \DateTimeImmutable());
        
        return $user;
    }

    private function createAssociation(User $owner, int $index): Association
    {
        $regions = ['Île-de-France', 'Auvergne-Rhône-Alpes', 'Occitanie', 'Nouvelle-Aquitaine', 'Hauts-de-France'];
        $departments = ['75', '69', '31', '33', '59'];
        $cities = ['Paris', 'Lyon', 'Toulouse', 'Bordeaux', 'Lille'];
        
        $association = new Association();
        $association->setOwner($owner);
        $association->setName("Association Protection Animale {$index}");
        $association->setSiret(str_pad((string)$index, 14, '0', STR_PAD_LEFT));
        $association->setEmailPublic("contact@asso{$index}.fr");
        $association->setPhonePublic("01" . str_pad((string)$index, 8, '0', STR_PAD_LEFT));
        $association->setWebsite("https://asso{$index}.fr");
        $association->setDescription("Association dédiée à la protection et à l'adoption des animaux. Nous accueillons chiens, chats et autres animaux en détresse.");
        $association->setSpeciesSupported(['dog', 'cat', 'rabbit', 'bird']);
        $association->setRegion($regions[($index - 1) % count($regions)]);
        $association->setDepartment($departments[($index - 1) % count($departments)]);
        $association->setCity($cities[($index - 1) % count($cities)]);
        $association->setPostalCode(str_pad((string)(75000 + $index), 5, '0', STR_PAD_LEFT));
        $association->setStreet("Rue de la Protection {$index}");
        $association->setLat(48.8566 + ($index * 0.01));
        $association->setLng(2.3522 + ($index * 0.01));
        $association->setIsApproved(true);
        $association->setCreatedAt(new \DateTimeImmutable());
        
        return $association;
    }

    private function createShelterCapacities(ObjectManager $manager, Association $association, int $index): void
    {
        $species = ['dog', 'cat', 'rabbit', 'bird'];
        
        foreach ($species as $speciesType) {
            $capacity = new ShelterCapacity();
            $capacity->setAssociation($association);
            $capacity->setSpecies($speciesType);
            $capacity->setCapacityTotal(rand(10, 50));
            $capacity->setCapacityAvailable(rand(0, 20));
            $capacity->setNotes("Capacité pour les {$speciesType}s");
            
            $manager->persist($capacity);
        }
    }

    private function createFosterProfile(User $user, int $index): FosterProfile
    {
        $regions = ['Île-de-France', 'Auvergne-Rhône-Alpes', 'Occitanie', 'Nouvelle-Aquitaine', 'Hauts-de-France'];
        $departments = ['75', '69', '31', '33', '59'];
        $cities = ['Paris', 'Lyon', 'Toulouse', 'Bordeaux', 'Lille'];
        
        $profile = new FosterProfile();
        $profile->setUser($user);
        $profile->setSpeciesAccepted(['dog', 'cat']);
        $profile->setMaxAnimals(rand(1, 3));
        $profile->setHasGarden(rand(0, 1) === 1);
        $profile->setChildrenAtHome(rand(0, 1) === 1);
        $profile->setOtherPets(rand(0, 1) === 1);
        $profile->setAvailabilityFrom(new \DateTimeImmutable('+1 week'));
        $profile->setAvailabilityTo(new \DateTimeImmutable('+6 months'));
        $profile->setRegion($regions[($index - 1) % count($regions)]);
        $profile->setDepartment($departments[($index - 1) % count($departments)]);
        $profile->setCity($cities[($index - 1) % count($cities)]);
        $profile->setPostalCode(str_pad((string)(75000 + $index), 5, '0', STR_PAD_LEFT));
        $profile->setStreet("Rue de l'Accueil {$index}");
        $profile->setLat(48.8566 + ($index * 0.01));
        $profile->setLng(2.3522 + ($index * 0.01));
        $profile->setNotes("Famille d'accueil disponible pour chiens et chats");
        $profile->setIsVisible(true);
        $profile->setCreatedAt(new \DateTimeImmutable());
        
        return $profile;
    }

    private function createVetProfile(User $user, int $index): VetProfile
    {
        $regions = ['Île-de-France', 'Auvergne-Rhône-Alpes', 'Occitanie', 'Nouvelle-Aquitaine', 'Hauts-de-France'];
        $departments = ['75', '69', '31', '33', '59'];
        $cities = ['Paris', 'Lyon', 'Toulouse', 'Bordeaux', 'Lille'];
        
        $profile = new VetProfile();
        $profile->setUser($user);
        $profile->setClinicName("Clinique Vétérinaire {$index}");
        $profile->setRppsOrLicense("RPPS" . str_pad((string)$index, 10, '0', STR_PAD_LEFT));
        $profile->setServices(['consultation', 'vaccination', 'sterilisation', 'urgence']);
        $profile->setFreeCareSlots(rand(5, 20));
        $profile->setRegion($regions[($index - 1) % count($regions)]);
        $profile->setDepartment($departments[($index - 1) % count($departments)]);
        $profile->setCity($cities[($index - 1) % count($cities)]);
        $profile->setPostalCode(str_pad((string)(75000 + $index), 5, '0', STR_PAD_LEFT));
        $profile->setStreet("Rue de la Santé {$index}");
        $profile->setLat(48.8566 + ($index * 0.01));
        $profile->setLng(2.3522 + ($index * 0.01));
        $profile->setIsApproved(true);
        $profile->setNotes("Vétérinaire proposant des soins solidaires");
        $profile->setCreatedAt(new \DateTimeImmutable());
        
        return $profile;
    }

    private function createAnimal(Association $association, int $index): Animal
    {
        $species = ['dog', 'cat', 'rabbit', 'bird'];
        $sexes = ['male', 'female'];
        $sizes = ['small', 'medium', 'large'];
        $colors = ['noir', 'blanc', 'marron', 'gris', 'roux', 'tricolore'];
        $statuses = ['available', 'pending', 'adopted'];
        
        $animal = new Animal();
        $animal->setAssociation($association);
        $animal->setName("Animal {$index}");
        $animal->setSpecies($species[array_rand($species)]);
        $animal->setSex($sexes[array_rand($sexes)]);
        $animal->setBirthDate(new \DateTimeImmutable('-' . rand(1, 5) . ' years'));
        $animal->setSize($sizes[array_rand($sizes)]);
        $animal->setColor($colors[array_rand($colors)]);
        $animal->setSterilized(rand(0, 1) === 1);
        $animal->setVaccinated(rand(0, 1) === 1);
        $animal->setIdentified(rand(0, 1) === 1);
        $animal->setDescription("Animal adorable et affectueux, parfait pour une famille. Il a été trouvé abandonné et recherche un foyer aimant.");
        $animal->setStatus($statuses[array_rand($statuses)]);
        $animal->setCreatedAt(new \DateTimeImmutable());
        
        return $animal;
    }

    private function createAnimalPhotos(ObjectManager $manager, Animal $animal, int $index): void
    {
        $photoCount = rand(1, 3);
        
        for ($i = 1; $i <= $photoCount; $i++) {
            $photo = new AnimalPhoto();
            $photo->setAnimal($animal);
            $photo->setPath("/uploads/animals/animal_{$index}_photo_{$i}.jpg");
            $photo->setIsMain($i === 1);
            $photo->setSortIndex($i);
            
            $manager->persist($photo);
        }
    }

    private function createAdoptionRequest(User $requester, Animal $animal, int $index): AdoptionRequest
    {
        $statuses = ['pending', 'approved', 'rejected'];
        
        $request = new AdoptionRequest();
        $request->setAnimal($animal);
        $request->setRequester($requester);
        $request->setMessage("Bonjour, je suis très intéressé par l'adoption de {$animal->getName()}. J'ai une maison avec jardin et beaucoup d'amour à donner. J'ai déjà eu des animaux et je suis prêt à m'engager sur le long terme.");
        $request->setStatus($statuses[array_rand($statuses)]);
        $request->setCreatedAt(new \DateTimeImmutable());
        
        if (rand(0, 1) === 1) {
            $request->setReviewedAt(new \DateTimeImmutable());
            $request->setRespondedAt(new \DateTimeImmutable());
        }
        
        return $request;
    }

    private function createDonation(User $donor, Association $association, int $index): Donation
    {
        $statuses = ['pending', 'paid', 'failed'];
        $amounts = [10, 20, 50, 100, 200];
        
        $donation = new Donation();
        $donation->setUser($donor);
        $donation->setAssociation($association);
        $donation->setAmount($amounts[array_rand($amounts)]);
        $donation->setCurrency('EUR');
        $donation->setStatus($statuses[array_rand($statuses)]);
        $donation->setCreatedAt(new \DateTimeImmutable());
        $donation->setMessage("Don pour soutenir votre action en faveur des animaux");
        
        if ($donation->getStatus() === 'paid') {
            $donation->setStripeCheckoutId('cs_test_' . str_pad((string)$index, 20, '0', STR_PAD_LEFT));
            $donation->setStripePaymentIntentId('pi_test_' . str_pad((string)$index, 20, '0', STR_PAD_LEFT));
        }
        
        return $donation;
    }

    private function createInKindDonation(User $donor, Association $association, int $index): InKindDonation
    {
        $types = ['food', 'toys', 'equipment', 'medicine', 'other'];
        $statuses = ['pending', 'accepted', 'rejected', 'delivered'];
        $regions = ['Île-de-France', 'Auvergne-Rhône-Alpes', 'Occitanie', 'Nouvelle-Aquitaine', 'Hauts-de-France'];
        $cities = ['Paris', 'Lyon', 'Toulouse', 'Bordeaux', 'Lille'];
        
        $donation = new InKindDonation();
        $donation->setUser($donor);
        $donation->setAssociation($association);
        $donation->setType($types[array_rand($types)]);
        $donation->setDescription("Don en nature de type {$types[array_rand($types)]}");
        $donation->setQuantity(rand(1, 10) . " unités");
        $donation->setStatus($statuses[array_rand($statuses)]);
        $donation->setCreatedAt(new \DateTimeImmutable());
        $donation->setNotes("Disponible immédiatement");
        $donation->setRegion($regions[($index - 1) % count($regions)]);
        $donation->setCity($cities[($index - 1) % count($cities)]);
        
        return $donation;
    }

    private function createWishlistItems(ObjectManager $manager, Association $association): void
    {
        $types = ['food', 'toys', 'equipment', 'medicine', 'other'];
        $urgencies = ['low', 'medium', 'high', 'critical'];
        
        for ($i = 1; $i <= rand(3, 8); $i++) {
            $item = new WishlistItem();
            $item->setAssociation($association);
            $item->setLabel("Besoin {$i} pour {$association->getName()}");
            $item->setType($types[array_rand($types)]);
            $item->setQuantityNeeded(rand(1, 50) . " unités");
            $item->setUrgency($urgencies[array_rand($urgencies)]);
            $item->setIsActive(true);
            $item->setCreatedAt(new \DateTimeImmutable());
            $item->setNotes("Besoin urgent pour l'association");
            
            $manager->persist($item);
        }
    }

    private function createReport(User $reporter, int $index): Report
    {
        $targetTypes = ['animal', 'association', 'user', 'adoption_request'];
        $reasons = ['inappropriate_content', 'fake_information', 'abuse', 'spam', 'other'];
        $statuses = ['pending', 'investigating', 'resolved', 'dismissed'];
        
        $report = new Report();
        $report->setReporter($reporter);
        $report->setTargetType($targetTypes[array_rand($targetTypes)]);
        $report->setTargetId('00000000-0000-0000-0000-000000000000');
        $report->setReason($reasons[array_rand($reasons)]);
        $report->setCreatedAt(new \DateTimeImmutable());
        $report->setStatus($statuses[array_rand($statuses)]);
        $report->setAdminNotes("Signalement traité par l'équipe de modération");
        
        if (rand(0, 1) === 1) {
            $report->setReviewedAt(new \DateTimeImmutable());
            $report->setClosedAt(new \DateTimeImmutable());
        }
        
        return $report;
    }
}
