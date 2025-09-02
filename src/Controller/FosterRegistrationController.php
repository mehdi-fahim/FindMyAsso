<?php

namespace App\Controller;

use App\Entity\FosterProfile;
use App\Entity\User;
use App\Form\FosterRegistrationStep1FormType;
use App\Form\FosterRegistrationStep2FormType;
use App\Form\FosterRegistrationStep3FormType;
use App\Form\FosterRegistrationStep4FormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

#[Route('/foster', name: 'foster_')]
class FosterRegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $step = (int) $request->query->get('step', 1);
        $data = $request->getSession()->get('foster_registration_data', []);

        $form = match($step) {
            1 => $this->createForm(FosterRegistrationStep1FormType::class, $data),
            2 => $this->createForm(FosterRegistrationStep2FormType::class, $data),
            3 => $this->createForm(FosterRegistrationStep3FormType::class, $data),
            4 => $this->createForm(FosterRegistrationStep4FormType::class, $data),
            default => $this->createForm(FosterRegistrationStep1FormType::class, $data)
        };

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $data = array_merge($data, $formData);
            $request->getSession()->set('foster_registration_data', $data);

            if ($step < 4) {
                return $this->redirectToRoute('foster_register', ['step' => $step + 1]);
            } else {
                // Créer l'utilisateur et le profil
                $user = $this->createUser($data);
                $fosterProfile = $this->createFosterProfile($user, $data);

                $this->entityManager->persist($user);
                $this->entityManager->persist($fosterProfile);
                $this->entityManager->flush();

                // Nettoyer la session
                $request->getSession()->remove('foster_registration_data');

                $this->addFlash('success', 'Votre inscription en tant que famille d\'accueil a été enregistrée avec succès !');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('foster/register.html.twig', [
            'form' => $form,
            'step' => $step,
            'data' => $data,
        ]);
    }

    private function createUser(array $data): User
    {
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFullName($data['fullName']);
        $user->setPhone($data['phone']);
        $user->setRoles(['ROLE_FOSTER']);
        $user->setIsVerified(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['plainPassword']);
        $user->setPassword($hashedPassword);

        return $user;
    }

    private function createFosterProfile(User $user, array $data): FosterProfile
    {
        $profile = new FosterProfile();
        $profile->setUser($user);
        $profile->setCapacity($data['capacity']);
        $profile->setSpeciesAccepted($data['speciesAccepted']);
        $profile->setRegion($data['region']);
        $profile->setDepartment($data['department']);
        $profile->setCity($data['city']);
        $profile->setPostalCode($data['postalCode']);
        $profile->setStreet($data['street']);
        $profile->setHousingType($data['housingType']);
        $profile->setHasGarden($data['hasGarden'] ?? false);
        $profile->setChildrenAtHome($data['childrenAtHome'] ?? false);
        $profile->setOtherPets($data['otherPets'] ?? false);
        $profile->setAvailabilityFrom($data['availabilityFrom'] ?? null);
        $profile->setAvailabilityTo($data['availabilityTo'] ?? null);
        $profile->setIsVisible($data['isVisible'] ?? true);
        $profile->setNotes($data['notes'] ?? null);

        // Stocker les réponses du questionnaire
        $questionnaireAnswers = [
            'questionnaire' => $data['questionnaireAnswers'] ?? '',
            'housing_type' => $data['housingType'],
            'has_garden' => $data['hasGarden'] ?? false,
            'children_at_home' => $data['childrenAtHome'] ?? false,
            'other_pets' => $data['otherPets'] ?? false,
        ];
        $profile->setQuestionnaireAnswers($questionnaireAnswers);

        return $profile;
    }
}
