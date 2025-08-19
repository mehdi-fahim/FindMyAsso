<?php

namespace App\Controller;

use App\Entity\Association;
use App\Repository\AssociationRepository;
use App\Repository\AnimalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssociationController extends AbstractController
{
    #[Route('/associations/{id}/animaux', name: 'app_association_animals')]
    public function associationAnimals(string $id, AssociationRepository $associationRepository, AnimalRepository $animalRepository): Response
    {
        $association = $associationRepository->find($id);
        
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }
        
        $animals = $animalRepository->findBy(['association' => $association, 'status' => 'disponible']);
        
        return $this->render('association/animals.html.twig', [
            'association' => $association,
            'animals' => $animals,
        ]);
    }
    
    #[Route('/associations/{id}/contact', name: 'app_association_contact')]
    public function associationContact(string $id, AssociationRepository $associationRepository): Response
    {
        $association = $associationRepository->find($id);
        
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }
        
        return $this->render('association/contact.html.twig', [
            'association' => $association,
        ]);
    }
    
    #[Route('/associations/{id}/don', name: 'app_association_donate')]
    public function associationDonate(string $id, AssociationRepository $associationRepository): Response
    {
        $association = $associationRepository->find($id);
        
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }
        
        return $this->render('association/donate.html.twig', [
            'association' => $association,
        ]);
    }
}
