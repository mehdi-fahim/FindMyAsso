<?php

namespace App\Controller;

use App\Repository\AssociationRepository;
use App\Repository\FosterProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('public/home.html.twig');
    }

    #[Route('/associations', name: 'app_associations')]
    public function associations(Request $request, AssociationRepository $associationRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $region = $request->query->get('region');
        $species = $request->query->get('species');
        
        $associations = $associationRepository->findByFilters($region, $species, $page);
        
        return $this->render('public/associations.html.twig', [
            'associations' => $associations,
            'regions' => $associationRepository->findAllRegions(),
            'species' => $associationRepository->findAllSpecies(),
            'currentRegion' => $region,
            'currentSpecies' => $species,
            'page' => $page,
        ]);
    }

    #[Route('/associations/{id}', name: 'app_association_show')]
    public function associationShow(string $id, AssociationRepository $associationRepository): Response
    {
        $association = $associationRepository->find($id);
        
        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }
        
        return $this->render('public/association_show.html.twig', [
            'association' => $association,
        ]);
    }



    #[Route('/familles-accueil', name: 'app_fosters')]
    public function fosters(Request $request, FosterProfileRepository $fosterRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $region = $request->query->get('region');
        $species = $request->query->get('species');
        
        $fosters = $fosterRepository->findByFilters($region, $species, $page);
        
        return $this->render('public/fosters.html.twig', [
            'fosters' => $fosters,
            'regions' => $fosterRepository->findAllRegions(),
            'species' => $fosterRepository->findAllSpecies(),
            'currentRegion' => $region,
            'currentSpecies' => $species,
        ]);
    }

    #[Route('/familles-accueil/inscription', name: 'app_foster_registration')]
    public function fosterRegistration(): Response
    {
        return $this->redirectToRoute('foster_register');
    }



    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('public/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('public/contact.html.twig');
    }
}
