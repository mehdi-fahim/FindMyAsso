<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin-simple')]
#[IsGranted('ROLE_ADMIN')]
class AdminControllerSimple extends AbstractController
{
    #[Route('/', name: 'app_admin_simple_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/simple_dashboard.html.twig', [
            'message' => 'Dashboard simplifié fonctionne !'
        ]);
    }
}
