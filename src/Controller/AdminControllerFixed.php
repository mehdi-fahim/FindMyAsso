<?php

namespace App\Controller;

use App\Entity\Association;
use App\Entity\FosterProfile;
use App\Entity\User;
use App\Entity\VetProfile;
use App\Entity\AdminComment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use DateTimeImmutable;

#[Route('/admin-fixed')]
#[IsGranted('ROLE_ADMIN')]
class AdminControllerFixed extends AbstractController
{
    #[Route('/', name: 'app_admin_fixed_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        try {
            // Compter les demandes en attente
            $pendingAssociations = $entityManager->getRepository(Association::class)->count(['isApproved' => false]);
            $pendingFosterFamilies = $entityManager->getRepository(FosterProfile::class)->count(['isVisible' => false]);
            $pendingVeterinarians = $entityManager->getRepository(VetProfile::class)->count(['isApproved' => false]);
            
            // Compter les utilisateurs non vérifiés
            $unverifiedUsers = $entityManager->getRepository(User::class)->count(['isVerified' => false]);

            return $this->render('admin/fixed_dashboard.html.twig', [
                'pendingAssociations' => $pendingAssociations,
                'pendingFosterFamilies' => $pendingFosterFamilies,
                'pendingVeterinarians' => $pendingVeterinarians,
                'unverifiedUsers' => $unverifiedUsers,
            ]);
        } catch (\Exception $e) {
            return new Response('Erreur dans le dashboard: ' . $e->getMessage());
        }
    }
}
