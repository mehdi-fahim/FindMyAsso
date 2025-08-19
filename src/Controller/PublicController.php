<?php

namespace App\Controller;

use App\Repository\AnimalRepository;
use App\Repository\AssociationRepository;
use App\Repository\FosterProfileRepository;
use App\Repository\VetProfileRepository;
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

    #[Route('/adoptions', name: 'app_adoptions')]
    public function adoptions(Request $request, AnimalRepository $animalRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $species = $request->query->get('species');
        $size = $request->query->get('size');
        $sex = $request->query->get('sex');
        $region = $request->query->get('region');
        
        $animals = $animalRepository->findByFilters($species, $size, $sex, $region, $page);
        
        return $this->render('public/adoptions.html.twig', [
            'animals' => $animals,
            'species' => $animalRepository->findAllSpecies(),
            'sizes' => $animalRepository->findAllSizes(),
            'sexes' => $animalRepository->findAllSexes(),
            'regions' => $animalRepository->findAllRegions(),
            'currentFilters' => [
                'species' => $species,
                'size' => $size,
                'sex' => $sex,
                'region' => $region,
            ],
        ]);
    }

    #[Route('/adoptions/{id}', name: 'app_adoption_show')]
    public function adoptionShow(string $id, AnimalRepository $animalRepository): Response
    {
        $animal = $animalRepository->find($id);
        
        if (!$animal) {
            throw $this->createNotFoundException('Animal non trouvé');
        }
        
        return $this->render('public/adoption_show.html.twig', [
            'animal' => $animal,
        ]);
    }

    #[Route('/donations', name: 'app_donations')]
    public function donations(AssociationRepository $associationRepository): Response
    {
        $associations = $associationRepository->findApproved();
        
        return $this->render('public/donations.html.twig', [
            'associations' => $associations,
        ]);
    }

    #[Route('/veterinaires-solidaires', name: 'app_vets')]
    public function vets(Request $request, VetProfileRepository $vetRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $region = $request->query->get('region');
        $service = $request->query->get('service');
        
        $vets = $vetRepository->findByFilters($region, $service, $page);
        
        return $this->render('public/vets.html.twig', [
            'vets' => $vets,
            'regions' => $vetRepository->findAllRegions(),
            'services' => $vetRepository->findAllServices(),
            'currentRegion' => $region,
            'currentService' => $service,
            'page' => $page,
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
        return $this->render('public/foster_registration.html.twig');
    }

    #[Route('/carte', name: 'app_map')]
    public function map(AssociationRepository $associationRepository, FosterProfileRepository $fosterRepository): Response
    {
        $associations = $associationRepository->findApproved();
        $fosters = $fosterRepository->findVisible();
        
        return $this->render('public/map.html.twig', [
            'associations' => $associations,
            'fosters' => $fosters,
        ]);
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
