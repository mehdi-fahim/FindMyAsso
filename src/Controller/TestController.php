<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return new Response('Test OK - Le contrôleur fonctionne');
    }

    #[Route('/test-admin', name: 'app_test_admin')]
    public function testAdmin(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new Response('Accès refusé - Pas admin');
        }
        
        return new Response('Test Admin OK - Vous êtes admin');
    }

    #[Route('/test-logout', name: 'app_test_logout')]
    public function testLogout(): Response
    {
        return new Response('
            <h1>Test Déconnexion</h1>
            <p>Si vous voyez cette page, la route fonctionne.</p>
            <p><a href="/logout">Cliquez ici pour vous déconnecter</a></p>
            <p><a href="/admin">Retour au dashboard</a></p>
        ');
    }

    #[Route('/test-login-template', name: 'app_test_login_template')]
    public function testLoginTemplate(): Response
    {
        return $this->render('security/login.html.twig', [
            'error' => null,
            'last_username' => ''
        ]);
    }

    #[Route('/test-auth', name: 'app_test_auth')]
    public function testAuth(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new Response('Non connecté');
        }
        
        $roles = $user->getRoles();
        $email = $user->getEmail();
        
        return new Response("
            <h1>Test Authentification</h1>
            <p><strong>Connecté :</strong> {$email}</p>
            <p><strong>Rôles :</strong> " . implode(', ', $roles) . "</p>
            <p><strong>ID :</strong> {$user->getId()}</p>
            <p><a href='/admin'>Dashboard Admin</a></p>
            <p><a href='/dashboard'>Dashboard Général</a></p>
            <p><a href='/logout'>Déconnexion</a></p>
        ");
    }

    #[Route('/test-user-admin', name: 'app_test_user_admin')]
    public function testUserAdmin(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'admin.findmyasso@gmail.com']);
        
        if (!$user) {
            return new Response('Utilisateur admin non trouvé dans la base de données');
        }
        
        $roles = $user->getRoles();
        $email = $user->getEmail();
        $id = $user->getId();
        $isVerified = $user->isVerified() ? 'Oui' : 'Non';
        
        // Test du mot de passe
        $testPassword = 'FindMyAsso93';
        $isPasswordValid = $passwordHasher->isPasswordValid($user, $testPassword) ? 'Valide' : 'Invalide';
        
        return new Response("
            <h1>Test Utilisateur Admin</h1>
            <p><strong>ID :</strong> {$id}</p>
            <p><strong>Email :</strong> {$email}</p>
            <p><strong>Rôles :</strong> " . implode(', ', $roles) . "</p>
            <p><strong>Vérifié :</strong> {$isVerified}</p>
            <p><strong>Mot de passe 'FindMyAsso93' :</strong> {$isPasswordValid}</p>
            <p><a href='/test'>Retour aux tests</a></p>
        ");
    }
}
