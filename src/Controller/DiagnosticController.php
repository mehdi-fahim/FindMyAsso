<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DiagnosticController extends AbstractController
{
    #[Route('/diagnostic', name: 'app_diagnostic')]
    public function diagnostic(EntityManagerInterface $entityManager): Response
    {
        $info = [];
        
        // Test 1: Contrôleur de base
        $info[] = "✅ Contrôleur de base fonctionne";
        
        // Test 2: Doctrine
        try {
            $info[] = "✅ Doctrine fonctionne";
            
            // Test 3: Base de données
            try {
                $connection = $entityManager->getConnection();
                $connection->executeQuery('SELECT 1');
                $info[] = "✅ Base de données accessible";
            } catch (\Exception $e) {
                $info[] = "❌ Base de données: " . $e->getMessage();
            }
        } catch (\Exception $e) {
            $info[] = "❌ Doctrine: " . $e->getMessage();
        }
        
        // Test 4: Twig
        try {
            $this->renderView('diagnostic/test.html.twig', ['test' => 'ok']);
            $info[] = "✅ Twig fonctionne";
        } catch (\Exception $e) {
            $info[] = "❌ Twig: " . $e->getMessage();
        }
        
        // Test 5: Sécurité
        try {
            $user = $this->getUser();
            if ($user) {
                $info[] = "✅ Utilisateur connecté: " . $user->getEmail();
                $info[] = "✅ Rôles: " . implode(', ', $user->getRoles());
            } else {
                $info[] = "ℹ️ Aucun utilisateur connecté";
            }
        } catch (\Exception $e) {
            $info[] = "❌ Sécurité: " . $e->getMessage();
        }
        
        return new Response('<h1>Diagnostic FindMyAsso</h1><ul><li>' . implode('</li><li>', $info) . '</li></ul>');
    }
    
    #[Route('/diagnostic/login', name: 'app_diagnostic_login')]
    public function testLogin(): Response
    {
        return new Response('
            <h1>Test de connexion</h1>
            <form action="/login" method="post">
                <p>Email: <input type="email" name="_username" value="admin.findmyasso@gmail.com"></p>
                <p>Mot de passe: <input type="password" name="_password" value="FindMyAsso93"></p>
                <p><button type="submit">Se connecter</button></p>
            </form>
            <p><a href="/diagnostic">Retour au diagnostic</a></p>
        ');
    }
}
