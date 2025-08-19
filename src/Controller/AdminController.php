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
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTimeImmutable;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        try {
            // Compter les demandes en attente
            $pendingAssociations = $entityManager->getRepository(Association::class)->count(['isApproved' => false]);
            $pendingFosterFamilies = $entityManager->getRepository(FosterProfile::class)->count(['isVisible' => false]);
            $pendingVeterinarians = $entityManager->getRepository(VetProfile::class)->count(['isApproved' => false]);
            
            // Compter les utilisateurs non vérifiés
            $unverifiedUsers = $entityManager->getRepository(User::class)->count(['isVerified' => false]);

            // Statistiques des dernières 24h
            $yesterday = new DateTimeImmutable('-24 hours');
            $recentAssociations = $entityManager->getRepository(Association::class)->count(['createdAt' => $yesterday]);
            $recentFosterFamilies = $entityManager->getRepository(FosterProfile::class)->count(['createdAt' => $yesterday]);
            $recentVeterinarians = $entityManager->getRepository(VetProfile::class)->count(['createdAt' => $yesterday]);

            return $this->render('admin/dashboard.html.twig', [
                'pendingAssociations' => $pendingAssociations,
                'pendingFosterFamilies' => $pendingFosterFamilies,
                'pendingVeterinarians' => $pendingVeterinarians,
                'unverifiedUsers' => $unverifiedUsers,
                'recentAssociations' => $recentAssociations,
                'recentFosterFamilies' => $recentFosterFamilies,
                'recentVeterinarians' => $recentVeterinarians,
            ]);
        } catch (\Exception $e) {
            return new Response('Erreur dans le dashboard: ' . $e->getMessage());
        }
    }

    #[Route('/associations', name: 'app_admin_associations')]
    public function associations(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $status = $request->query->get('status', 'all');
            $search = $request->query->get('search', '');
            $region = $request->query->get('region', '');
            $page = $request->query->getInt('page', 1);
            $limit = 20;

            $qb = $entityManager->getRepository(Association::class)->createQueryBuilder('a')
                ->leftJoin('a.user', 'u')
                ->orderBy('a.createdAt', 'DESC');

            // Filtres
            if ($status !== 'all') {
                $qb->andWhere('a.isApproved = :status')
                   ->setParameter('status', $status === 'approved');
            }

            if ($search) {
                $qb->andWhere('a.name LIKE :search OR a.emailPublic LIKE :search OR u.email LIKE :search')
                   ->setParameter('search', '%' . $search . '%');
            }

            if ($region) {
                $qb->andWhere('a.region = :region')
                   ->setParameter('region', $region);
            }

            $associations = $qb->setFirstResult(($page - 1) * $limit)
                              ->setMaxResults($limit)
                              ->getQuery()
                              ->getResult();

            $totalAssociations = $entityManager->getRepository(Association::class)->count([]);
            $totalPages = ceil($totalAssociations / $limit);

            // Récupérer les régions pour le filtre
            $regions = $entityManager->getRepository(Association::class)->findAllRegions();

            return $this->render('admin/associations.html.twig', [
                'associations' => $associations,
                'currentStatus' => $status,
                'currentSearch' => $search,
                'currentRegion' => $region,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'regions' => $regions,
            ]);
        } catch (\Exception $e) {
            return new Response('Erreur dans les associations: ' . $e->getMessage());
        }
    }

    #[Route('/associations/{id}', name: 'app_admin_association_show')]
    public function associationShow(Association $association): Response
    {
        return $this->render('admin/association_show.html.twig', [
            'association' => $association,
        ]);
    }

    #[Route('/associations/{id}/approve', name: 'app_admin_association_approve')]
    public function approveAssociation(
        Association $association, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Request $request
    ): Response {
        try {
            $comment = $request->request->get('comment', '');
            
            $association->setIsApproved(true);
            $association->setIsActive(true);
            $association->setApprovedAt(new DateTimeImmutable());
            
            // Activer le compte utilisateur
            $user = $association->getUser();
            $user->setIsVerified(true);
            
            // Ajouter un commentaire d'administration
            if ($comment) {
                $adminComment = new AdminComment();
                $adminComment->setEntityType('association');
                $adminComment->setEntityId($association->getId());
                $adminComment->setComment($comment);
                $adminComment->setAdmin($this->getUser());
                $adminComment->setCreatedAt(new DateTimeImmutable());
                $adminComment->setAction('approval');
                
                $entityManager->persist($adminComment);
            }
            
            $entityManager->flush();

            // Envoyer un email de confirmation
            $this->sendApprovalEmail($mailer, $user->getEmail(), 'association', $association->getName());

            $this->addFlash('success', 'L\'association "' . $association->getName() . '" a été approuvée avec succès.');

            return $this->redirectToRoute('app_admin_associations');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    #[Route('/associations/{id}/reject', name: 'app_admin_association_reject')]
    public function rejectAssociation(
        Association $association, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Request $request
    ): Response {
        try {
            $comment = $request->request->get('comment', '');
            
            // Ajouter un commentaire d'administration avant suppression
            if ($comment) {
                $adminComment = new AdminComment();
                $adminComment->setEntityType('association');
                $adminComment->setEntityId($association->getId());
                $adminComment->setComment($comment);
                $adminComment->setAdmin($this->getUser());
                $adminComment->setCreatedAt(new DateTimeImmutable());
                $adminComment->setAction('rejection');
                
                $entityManager->persist($adminComment);
            }
            
            // Envoyer un email de rejet
            $this->sendRejectionEmail($mailer, $association->getUser()->getEmail(), 'association', $association->getName(), $comment);
            
            $entityManager->remove($association);
            $entityManager->flush();

            $this->addFlash('success', 'L\'association "' . $association->getName() . '" a été rejetée et supprimée.');

            return $this->redirectToRoute('app_admin_associations');
        } catch (\Exception $e) {
            return new Response('Erreur lors du rejet: ' . $e->getMessage());
        }
    }

    #[Route('/associations/{id}/request-info', name: 'app_admin_association_request_info')]
    public function requestInfoAssociation(
        Association $association, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Request $request
    ): Response {
        try {
            $comment = $request->request->get('comment', '');
            
            // Ajouter un commentaire d'administration
            $adminComment = new AdminComment();
            $adminComment->setEntityType('association');
            $adminComment->setEntityId($association->getId());
            $adminComment->setComment($comment);
            $adminComment->setAdmin($this->getUser());
            $adminComment->setCreatedAt(new DateTimeImmutable());
            $adminComment->setAction('info_request');
            
            $entityManager->persist($adminComment);
            $entityManager->flush();

            // Envoyer un email de demande d'information
            $this->sendInfoRequestEmail($mailer, $association->getUser()->getEmail(), 'association', $association->getName(), $comment);

            $this->addFlash('info', 'Une demande d\'information a été envoyée à l\'association "' . $association->getName() . '".');

            return $this->redirectToRoute('app_admin_association_show', ['id' => $association->getId()]);
        } catch (\Exception $e) {
            return new Response('Erreur lors de la demande d\'info: ' . $e->getMessage());
        }
    }

    #[Route('/foster-families', name: 'app_admin_foster_families')]
    public function fosterFamilies(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $status = $request->query->get('status', 'all');
            $search = $request->query->get('search', '');
            $region = $request->query->get('region', '');
            $page = $request->query->getInt('page', 1);
            $limit = 20;

            $qb = $entityManager->getRepository(FosterProfile::class)->createQueryBuilder('f')
                ->leftJoin('f.user', 'u')
                ->orderBy('f.createdAt', 'DESC');

            // Filtres
            if ($status !== 'all') {
                $qb->andWhere('f.isVisible = :status')
                   ->setParameter('status', $status === 'approved');
            }

            if ($search) {
                $qb->andWhere('u.fullName LIKE :search OR u.email LIKE :search')
                   ->setParameter('search', '%' . $search . '%');
            }

            if ($region) {
                $qb->andWhere('f.region = :region')
                   ->setParameter('region', $region);
            }

            $fosterProfiles = $qb->setFirstResult(($page - 1) * $limit)
                                 ->setMaxResults($limit)
                                 ->getQuery()
                                 ->getResult();

            $totalFosterProfiles = $entityManager->getRepository(FosterProfile::class)->count([]);
            $totalPages = ceil($totalFosterProfiles / $limit);

            // Récupérer les régions pour le filtre
            $regions = $entityManager->getRepository(FosterProfile::class)->findAllRegions();

            return $this->render('admin/foster_families.html.twig', [
                'fosterProfiles' => $fosterProfiles,
                'currentStatus' => $status,
                'currentSearch' => $search,
                'currentRegion' => $region,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'regions' => $regions,
            ]);
        } catch (\Exception $e) {
            return new Response('Erreur dans les familles d\'accueil: ' . $e->getMessage());
        }
    }

    #[Route('/foster-families/{id}', name: 'app_admin_foster_family_show')]
    public function fosterFamilyShow(FosterProfile $fosterProfile): Response
    {
        return $this->render('admin/foster_family_show.html.twig', [
            'fosterProfile' => $fosterProfile,
        ]);
    }

    #[Route('/foster-families/{id}/approve', name: 'app_admin_foster_family_approve')]
    public function approveFosterFamily(
        FosterProfile $fosterProfile, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Request $request
    ): Response {
        try {
            $comment = $request->request->get('comment', '');
            
            $fosterProfile->setIsVisible(true);
            
            // Activer le compte utilisateur
            $user = $fosterProfile->getUser();
            $user->setIsVerified(true);
            
            // Ajouter un commentaire d'administration
            if ($comment) {
                $adminComment = new AdminComment();
                $adminComment->setEntityType('foster_profile');
                $adminComment->setEntityId($fosterProfile->getId());
                $adminComment->setComment($comment);
                $adminComment->setAdmin($this->getUser());
                $adminComment->setCreatedAt(new DateTimeImmutable());
                $adminComment->setAction('approval');
                
                $entityManager->persist($adminComment);
            }
            
            $entityManager->flush();

            // Envoyer un email de confirmation
            $this->sendApprovalEmail($mailer, $user->getEmail(), 'foster_family', $user->getFullName());

            $this->addFlash('success', 'La famille d\'accueil "' . $user->getFullName() . '" a été approuvée avec succès.');

            return $this->redirectToRoute('app_admin_foster_families');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    #[Route('/foster-families/{id}/reject', name: 'app_admin_foster_family_reject')]
    public function rejectFosterFamily(
        FosterProfile $fosterProfile, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Request $request
    ): Response {
        try {
            $comment = $request->request->get('comment', '');
            
            // Ajouter un commentaire d'administration avant suppression
            if ($comment) {
                $adminComment = new AdminComment();
                $adminComment->setEntityType('foster_profile');
                $adminComment->setEntityId($fosterProfile->getId());
                $adminComment->setComment($comment);
                $adminComment->setAdmin($this->getUser());
                $adminComment->setCreatedAt(new DateTimeImmutable());
                $adminComment->setAction('rejection');
                
                $entityManager->persist($adminComment);
            }
            
            // Envoyer un email de rejet
            $this->sendRejectionEmail($mailer, $fosterProfile->getUser()->getEmail(), 'foster_family', $fosterProfile->getUser()->getFullName(), $comment);
            
            $entityManager->remove($fosterProfile);
            $entityManager->flush();

            $this->addFlash('success', 'La famille d\'accueil "' . $fosterProfile->getUser()->getFullName() . '" a été rejetée et supprimée.');

            return $this->redirectToRoute('app_admin_foster_families');
        } catch (\Exception $e) {
            return new Response('Erreur lors du rejet: ' . $e->getMessage());
        }
    }

    #[Route('/veterinarians', name: 'app_admin_veterinarians')]
    public function veterinarians(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $status = $request->query->get('status', 'all');
            $search = $request->query->get('search', '');
            $region = $request->query->get('region', '');
            $page = $request->query->getInt('page', 1);
            $limit = 20;

            $qb = $entityManager->getRepository(VetProfile::class)->createQueryBuilder('v')
                ->leftJoin('v.user', 'u')
                ->orderBy('v.createdAt', 'DESC');

            // Filtres
            if ($status !== 'all') {
                $qb->andWhere('v.isApproved = :status')
                   ->setParameter('status', $status === 'approved');
            }

            if ($search) {
                $qb->andWhere('u.fullName LIKE :search OR u.email LIKE :search')
                   ->setParameter('search', '%' . $search . '%');
            }

            if ($region) {
                $qb->andWhere('v.region = :region')
                   ->setParameter('region', $region);
            }

            $vetProfiles = $qb->setFirstResult(($page - 1) * $limit)
                              ->setMaxResults($limit)
                              ->getQuery()
                              ->getResult();

            $totalVetProfiles = $entityManager->getRepository(VetProfile::class)->count([]);
            $totalPages = ceil($totalVetProfiles / $limit);

            // Récupérer les régions pour le filtre
            $regions = $entityManager->getRepository(VetProfile::class)->findAllRegions();

            return $this->render('admin/veterinarians.html.twig', [
                'vetProfiles' => $vetProfiles,
                'currentStatus' => $status,
                'currentSearch' => $search,
                'currentRegion' => $region,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'regions' => $regions,
            ]);
        } catch (\Exception $e) {
            return new Response('Erreur dans les vétérinaires: ' . $e->getMessage());
        }
    }

    #[Route('/veterinarians/{id}', name: 'app_admin_veterinarian_show')]
    public function veterinarianShow(VetProfile $vetProfile): Response
    {
        return $this->render('admin/veterinarian_show.html.twig', [
            'vetProfile' => $vetProfile,
        ]);
    }

    #[Route('/veterinarians/{id}/approve', name: 'app_admin_veterinarian_approve')]
    public function approveVeterinarian(
        VetProfile $vetProfile, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Request $request
    ): Response {
        try {
            $comment = $request->request->get('comment', '');
            
            $vetProfile->setIsApproved(true);
            $vetProfile->setIsActive(true);
            $vetProfile->setApprovedAt(new DateTimeImmutable());
            
            // Activer le compte utilisateur
            $user = $vetProfile->getUser();
            $user->setIsVerified(true);
            
            // Ajouter un commentaire d'administration
            if ($comment) {
                $adminComment = new AdminComment();
                $adminComment->setEntityType('vet_profile');
                $adminComment->setEntityId($vetProfile->getId());
                $adminComment->setComment($comment);
                $adminComment->setAdmin($this->getUser());
                $adminComment->setCreatedAt(new DateTimeImmutable());
                $adminComment->setAction('approval');
                
                $entityManager->persist($adminComment);
            }
            
            $entityManager->flush();

            // Envoyer un email de confirmation
            $this->sendApprovalEmail($mailer, $user->getEmail(), 'veterinarian', $user->getFullName());

            $this->addFlash('success', 'Le vétérinaire "' . $user->getFullName() . '" a été approuvé avec succès.');

            return $this->redirectToRoute('app_admin_veterinarians');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    #[Route('/veterinarians/{id}/reject', name: 'app_admin_veterinarian_reject')]
    public function rejectVeterinarian(
        VetProfile $vetProfile, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Request $request
    ): Response {
        try {
            $comment = $request->request->get('comment', '');
            
            // Ajouter un commentaire d'administration avant suppression
            if ($comment) {
                $adminComment = new AdminComment();
                $adminComment->setEntityType('vet_profile');
                $adminComment->setEntityId($vetProfile->getId());
                $adminComment->setComment($comment);
                $adminComment->setAdmin($this->getUser());
                $adminComment->setCreatedAt(new DateTimeImmutable());
                $adminComment->setAction('rejection');
                
                $entityManager->persist($adminComment);
            }
            
            // Envoyer un email de rejet
            $this->sendRejectionEmail($mailer, $vetProfile->getUser()->getEmail(), 'veterinarian', $vetProfile->getUser()->getFullName(), $comment);
            
            $entityManager->remove($vetProfile);
            $entityManager->flush();

            $this->addFlash('success', 'Le vétérinaire "' . $vetProfile->getUser()->getFullName() . '" a été rejeté et supprimé.');

            return $this->redirectToRoute('app_admin_veterinarians');
        } catch (\Exception $e) {
            return new Response('Erreur lors du rejet: ' . $e->getMessage());
        }
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $status = $request->query->get('status', 'all');
            $search = $request->query->get('search', '');
            $role = $request->query->get('role', 'all');
            $page = $request->query->getInt('page', 1);
            $limit = 20;

            $qb = $entityManager->getRepository(User::class)->createQueryBuilder('u')
                ->orderBy('u.createdAt', 'DESC');

            // Filtres
            if ($status !== 'all') {
                $qb->andWhere('u.isVerified = :status')
                   ->setParameter('status', $status === 'verified');
            }

            if ($search) {
                $qb->andWhere('u.email LIKE :search OR u.fullName LIKE :search')
                   ->setParameter('search', '%' . $search . '%');
            }

            if ($role !== 'all') {
                $qb->andWhere('u.roles LIKE :role')
                   ->setParameter('role', '%' . $role . '%');
            }

            $users = $qb->setFirstResult(($page - 1) * $limit)
                        ->setMaxResults($limit)
                        ->getQuery()
                        ->getResult();

            $totalUsers = $entityManager->getRepository(User::class)->count([]);
            $totalPages = ceil($totalUsers / $limit);

            return $this->render('admin/users.html.twig', [
                'users' => $users,
                'currentStatus' => $status,
                'currentSearch' => $search,
                'currentRole' => $role,
                'currentPage' => $page,
                'totalPages' => $totalPages,
            ]);
        } catch (\Exception $e) {
            return new Response('Erreur dans les utilisateurs: ' . $e->getMessage());
        }
    }

    #[Route('/users/{id}/verify', name: 'app_admin_user_verify')]
    public function verifyUser(User $user, EntityManagerInterface $entityManager): Response
    {
        try {
            $user->setIsVerified(true);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur "' . $user->getEmail() . '" a été vérifié avec succès.');

            return $this->redirectToRoute('app_admin_users');
        } catch (\Exception $e) {
            return new Response('Erreur lors de la vérification: ' . $e->getMessage());
        }
    }

    #[Route('/users/{id}/delete', name: 'app_admin_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur "' . $user->getEmail() . '" a été supprimé avec succès.');

            return $this->redirectToRoute('app_admin_users');
        } catch (\Exception $e) {
            return new Response('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // Méthodes privées pour l'envoi d'emails
    private function sendApprovalEmail(MailerInterface $mailer, string $email, string $type, string $name): void
    {
        $subject = 'Votre inscription a été approuvée - FindMyAsso';
        $htmlContent = $this->renderView('emails/approval.html.twig', [
            'type' => $type,
            'name' => $name,
        ]);

        $email = (new Email())
            ->from('noreply@findmyasso.fr')
            ->to($email)
            ->subject($subject)
            ->html($htmlContent);

        $mailer->send($email);
    }

    private function sendRejectionEmail(MailerInterface $mailer, string $email, string $type, string $name, string $comment): void
    {
        $subject = 'Information concernant votre inscription - FindMyAsso';
        $htmlContent = $this->renderView('emails/rejection.html.twig', [
            'type' => $type,
            'name' => $name,
            'comment' => $comment,
        ]);

        $email = (new Email())
            ->from('noreply@findmyasso.fr')
            ->to($email)
            ->subject($subject)
            ->html($htmlContent);

        $mailer->send($email);
    }

    private function sendInfoRequestEmail(MailerInterface $mailer, string $email, string $type, string $name, string $comment): void
    {
        $subject = 'Demande d\'information - FindMyAsso';
        $htmlContent = $this->renderView('emails/info_request.html.twig', [
            'type' => $type,
            'name' => $name,
            'comment' => $comment,
        ]);

        $email = (new Email())
            ->from('noreply@findmyasso.fr')
            ->to($email)
            ->subject($subject)
            ->html($htmlContent);

        $mailer->send($email);
    }
}
