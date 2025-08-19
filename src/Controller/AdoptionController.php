<?php

namespace App\Controller;

use App\Entity\AdoptionRequest;
use App\Entity\Animal;
use App\Form\AdoptionRequestFormType;
use App\Repository\AnimalRepository;
use App\Repository\AdoptionRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdoptionController extends AbstractController
{
    #[Route('/adoptions/{animalId}/demande', name: 'app_adoption_request')]
    public function adoptionRequest(
        string $animalId, 
        Request $request, 
        AnimalRepository $animalRepository,
        AdoptionRequestRepository $adoptionRequestRepository
    ): Response {
        $animal = $animalRepository->find($animalId);
        
        if (!$animal) {
            throw $this->createNotFoundException('Animal non trouvé');
        }
        
        if ($animal->getStatus() !== 'disponible') {
            $this->addFlash('error', 'Cet animal n\'est plus disponible à l\'adoption.');
            return $this->redirectToRoute('app_adoption_show', ['id' => $animalId]);
        }
        
        // Vérifier si l'utilisateur a déjà fait une demande pour cet animal
        if ($this->getUser()) {
            $existingRequest = $adoptionRequestRepository->findOneBy([
                'animal' => $animal,
                'user' => $this->getUser()
            ]);
            
            if ($existingRequest) {
                $this->addFlash('info', 'Vous avez déjà fait une demande d\'adoption pour cet animal.');
                return $this->redirectToRoute('app_adoption_show', ['id' => $animalId]);
            }
        }
        
        $adoptionRequest = new AdoptionRequest();
        $adoptionRequest->setAnimal($animal);
        
        if ($this->getUser()) {
            $adoptionRequest->setUser($this->getUser());
        }
        
        $form = $this->createForm(AdoptionRequestFormType::class, $adoptionRequest);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $adoptionRequest->setStatus('en_attente');
            $adoptionRequest->setRequestDate(new \DateTime());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adoptionRequest);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre demande d\'adoption a été envoyée avec succès !');
            return $this->redirectToRoute('app_adoption_show', ['id' => $animalId]);
        }
        
        return $this->render('adoption/request.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/adoptions/mes-demandes', name: 'app_my_adoption_requests')]
    #[IsGranted('ROLE_USER')]
    public function myAdoptionRequests(AdoptionRequestRepository $adoptionRequestRepository): Response
    {
        $requests = $adoptionRequestRepository->findBy(['user' => $this->getUser()], ['requestDate' => 'DESC']);
        
        return $this->render('adoption/my_requests.html.twig', [
            'requests' => $requests,
        ]);
    }
    
    #[Route('/adoptions/demande/{id}', name: 'app_adoption_request_show')]
    #[IsGranted('ROLE_USER')]
    public function adoptionRequestShow(string $id, AdoptionRequestRepository $adoptionRequestRepository): Response
    {
        $adoptionRequest = $adoptionRequestRepository->find($id);
        
        if (!$adoptionRequest) {
            throw $this->createNotFoundException('Demande d\'adoption non trouvée');
        }
        
        // Vérifier que l'utilisateur peut voir cette demande
        if ($adoptionRequest->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette demande d\'adoption');
        }
        
        return $this->render('adoption/request_show.html.twig', [
            'adoptionRequest' => $adoptionRequest,
        ]);
    }
}
