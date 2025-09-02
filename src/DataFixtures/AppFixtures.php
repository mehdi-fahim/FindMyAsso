<?php

namespace App\DataFixtures;

use App\Entity\Association;
use App\Entity\FosterProfile;
use App\Entity\Report;
use App\Entity\ShelterCapacity;
use App\Entity\User;
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
        $housingTypes = ['APARTMENT', 'HOUSE', 'FARM', 'OTHER'];
        $speciesOptions = [['DOG'], ['CAT'], ['DOG', 'CAT'], ['OTHER']];
        
        $profile = new FosterProfile();
        $profile->setUser($user);
        $profile->setSpeciesAccepted($speciesOptions[array_rand($speciesOptions)]);
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
        $profile->setHousingType($housingTypes[array_rand($housingTypes)]);
        $profile->setNotes("Famille d'accueil disponible pour chiens et chats");
        $profile->setIsVisible(true);
        $profile->setCreatedAt(new \DateTimeImmutable());
        
        // Ajouter les réponses du questionnaire
        $questionnaireAnswers = [
            'questionnaire' => "J'ai de l'expérience avec les animaux et je souhaite aider les animaux en détresse. J'ai un environnement calme et sécurisé pour accueillir temporairement des animaux.",
            'housing_type' => $profile->getHousingType(),
            'has_garden' => $profile->isHasGarden(),
            'children_at_home' => $profile->isChildrenAtHome(),
            'other_pets' => $profile->isOtherPets(),
            'experience' => 'Expérimenté',
            'time_at_home' => 'Temps plein',
            'motivation' => 'Aider les animaux en détresse et leur donner une seconde chance'
        ];
        $profile->setQuestionnaireAnswers($questionnaireAnswers);
        
        return $profile;
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
