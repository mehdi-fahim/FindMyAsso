<?php

namespace App\Controller;

use App\Entity\AdoptionRequest;
use App\Entity\Animal;
use App\Entity\Association;
use App\Entity\Donation;
use App\Entity\FosterProfile;
use App\Entity\InKindDonation;
use App\Entity\Report;
use App\Entity\User;
use App\Entity\VetProfile;
use App\Repository\AdoptionRequestRepository;
use App\Repository\AnimalRepository;
use App\Repository\AssociationRepository;
use App\Repository\DonationRepository;
use App\Repository\FosterProfileRepository;
use App\Repository\InKindDonationRepository;
use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use App\Repository\VetProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }
        
        if ($this->isGranted('ROLE_ASSOCIATION')) {
            return $this->redirectToRoute('app_association_dashboard');
        }
        
        if ($this->isGranted('ROLE_FOSTER_FAMILY')) {
            return $this->redirectToRoute('app_foster_dashboard');
        }
        
        if ($this->isGranted('ROLE_VETERINARIAN')) {
            return $this->redirectToRoute('app_vet_dashboard');
        }
        
        // Dashboard par défaut pour les autres rôles
        return $this->render('dashboard/default.html.twig', [
            'user' => $user,
        ]);
    }



    #[Route('/association', name: 'app_association_dashboard')]
    #[IsGranted('ROLE_ASSOCIATION')]
    public function associationDashboard(
        AnimalRepository $animalRepository,
        AdoptionRequestRepository $adoptionRepository,
        DonationRepository $donationRepository,
        InKindDonationRepository $inKindDonationRepository
    ): Response {
        $user = $this->getUser();
        $association = $user->getAssociation();
        
        if (!$association) {
            throw $this->createNotFoundException('Profil d\'association non trouvé');
        }
        
        $animals = $animalRepository->findByAssociation($association->getId());
        $pendingAdoptions = $adoptionRepository->findPendingByAssociation($association->getId());
        $recentDonations = $donationRepository->findRecentByAssociation($association->getId(), 5);
        $inKindDonations = $inKindDonationRepository->findByAssociation($association->getId());
        
        return $this->render('dashboard/association.html.twig', [
            'association' => $association,
            'animals' => $animals,
            'pendingAdoptions' => $pendingAdoptions,
            'recentDonations' => $recentDonations,
            'inKindDonations' => $inKindDonations,
        ]);
    }

    #[Route('/foster', name: 'app_foster_dashboard')]
    #[IsGranted('ROLE_FOSTER')]
    public function fosterDashboard(
        FosterProfileRepository $fosterRepository,
        AnimalRepository $animalRepository
    ): Response {
        $user = $this->getUser();
        $fosterProfile = $user->getFosterProfile();
        
        if (!$fosterProfile) {
            return $this->redirectToRoute('app_foster_profile_create');
        }
        
        $availableAnimals = $animalRepository->findAvailableForFoster($fosterProfile->getSpeciesAccepted());
        
        return $this->render('dashboard/foster.html.twig', [
            'fosterProfile' => $fosterProfile,
            'availableAnimals' => $availableAnimals,
        ]);
    }

    #[Route('/vet', name: 'app_vet_dashboard')]
    #[IsGranted('ROLE_VET')]
    public function vetDashboard(
        VetProfileRepository $vetRepository
    ): Response {
        $user = $this->getUser();
        $vetProfile = $user->getVetProfile();
        
        if (!$vetProfile) {
            return $this->redirectToRoute('app_vet_profile_create');
        }
        
        return $this->render('dashboard/vet.html.twig', [
            'vetProfile' => $vetProfile,
        ]);
    }

    #[Route('/donor', name: 'app_donor_dashboard')]
    #[IsGranted('ROLE_DONOR')]
    public function donorDashboard(
        DonationRepository $donationRepository,
        InKindDonationRepository $inKindDonationRepository
    ): Response {
        $user = $this->getUser();
        
        $monetaryDonations = $donationRepository->findByUser($user->getId());
        $inKindDonations = $inKindDonationRepository->findByUser($user->getId());
        
        return $this->render('dashboard/donor.html.twig', [
            'user' => $user,
            'monetaryDonations' => $monetaryDonations,
            'inKindDonations' => $inKindDonations,
        ]);
    }

    #[Route('/adopter', name: 'app_adopter_dashboard')]
    #[IsGranted('ROLE_ADOPTER')]
    public function adopterDashboard(
        AdoptionRequestRepository $adoptionRepository
    ): Response {
        $user = $this->getUser();
        
        $adoptionRequests = $adoptionRepository->findByRequester($user->getId());
        
        return $this->render('dashboard/adopter.html.twig', [
            'user' => $user,
            'adoptionRequests' => $adoptionRequests,
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        
        return $this->render('dashboard/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser();
        
        // TODO: Créer le formulaire d'édition de profil
        
        return $this->render('dashboard/edit_profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/notifications', name: 'app_notifications')]
    public function notifications(): Response
    {
        $user = $this->getUser();
        
        // TODO: Implémenter le système de notifications
        
        return $this->render('dashboard/notifications.html.twig', [
            'user' => $user,
            'notifications' => [],
        ]);
    }
}
