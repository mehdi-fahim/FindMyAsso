<?php

namespace App\Controller;

use App\Entity\Association;
use App\Entity\FosterProfile;
use App\Entity\User;
use App\Entity\VetProfile;
use App\Form\AssociationRegistrationFormType;
use App\Form\FosterFamilyRegistrationFormType;
use App\Form\VetRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('registration/choose_type.html.twig');
    }

    #[Route('/register/association', name: 'app_register_association')]
    public function registerAssociation(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $association = new Association();
        $form = $this->createForm(AssociationRegistrationFormType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que les mots de passe correspondent
            if ($form->get('plainPassword')->getData() !== $form->get('confirmPassword')->getData()) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('registration/association.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Créer le compte utilisateur
            $user = new User();
            $user->setEmail($form->get('userEmail')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_ASSOCIATION']);
            $user->setIsVerified(false);

            // Gérer le logo
            $logoFile = $form->get('logo')->getData();
            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

                try {
                    $logoFile->move(
                        $this->getParameter('associations_directory'),
                        $newFilename
                    );
                    $association->setLogo($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du logo.');
                }
            }

            // Configurer l'association
            $association->setIsApproved(false);
            $association->setIsActive(false);
            $association->setCreatedAt(new \DateTime());
            $association->setUser($user);

            // Sauvegarder
            $entityManager->persist($user);
            $entityManager->persist($association);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande d\'inscription a été envoyée avec succès ! Elle sera examinée par nos administrateurs dans les plus brefs délais.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/association.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register/foster-family', name: 'app_register_foster_family')]
    public function registerFosterFamily(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $fosterProfile = new FosterProfile();
        $form = $this->createForm(FosterFamilyRegistrationFormType::class, $fosterProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que les mots de passe correspondent
            if ($form->get('plainPassword')->getData() !== $form->get('confirmPassword')->getData()) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('registration/foster_family.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Créer le compte utilisateur
            $user = new User();
            $user->setEmail($form->get('userEmail')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_FOSTER_FAMILY']);
            $user->setIsVerified(false);

            // Gérer la photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('users_directory'),
                        $newFilename
                    );
                    $fosterProfile->setPhoto($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de la photo.');
                }
            }

            // Configurer le profil famille d'accueil
            $fosterProfile->setIsVisible(false);
            $fosterProfile->setIsApproved(false);
            $fosterProfile->setCreatedAt(new \DateTime());
            $fosterProfile->setUser($user);

            // Sauvegarder
            $entityManager->persist($user);
            $entityManager->persist($fosterProfile);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande d\'inscription a été envoyée avec succès ! Elle sera examinée par nos administrateurs dans les plus brefs délais.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/foster_family.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register/veterinarian', name: 'app_register_veterinarian')]
    public function registerVeterinarian(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $vetProfile = new VetProfile();
        $form = $this->createForm(VetRegistrationFormType::class, $vetProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que les mots de passe correspondent
            if ($form->get('plainPassword')->getData() !== $form->get('confirmPassword')->getData()) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('registration/veterinarian.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Créer le compte utilisateur
            $user = new User();
            $user->setEmail($form->get('userEmail')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_VETERINARIAN']);
            $user->setIsVerified(false);

            // Gérer la photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('users_directory'),
                        $newFilename
                    );
                    $vetProfile->setPhoto($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de la photo.');
                }
            }

            // Configurer le profil vétérinaire
            $vetProfile->setIsApproved(false);
            $vetProfile->setIsActive(false);
            $vetProfile->setCreatedAt(new \DateTime());
            $vetProfile->setUser($user);

            // Sauvegarder
            $entityManager->persist($user);
            $entityManager->persist($vetProfile);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande d\'inscription a été envoyée avec succès ! Elle sera examinée par nos administrateurs dans les plus brefs délais.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/veterinarian.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
