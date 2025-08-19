<?php

namespace App\Form;

use App\Entity\VetProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VetRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Informations personnelles
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet *',
                'attr' => [
                    'placeholder' => 'Dr. Votre Nom',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom complet est obligatoire']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('title', ChoiceType::class, [
                'label' => 'Titre *',
                'choices' => [
                    'Dr.' => 'Dr.',
                    'Dr. Vétérinaire' => 'Dr. Vétérinaire',
                    'Pr.' => 'Pr.',
                    'Autre' => 'Autre',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire']),
                ],
            ])
            
            // Contact
            ->add('email', EmailType::class, [
                'label' => 'Email professionnel *',
                'attr' => [
                    'placeholder' => 'dr.nom@clinique-vet.fr',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone professionnel *',
                'attr' => [
                    'placeholder' => '01 23 45 67 89',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire']),
                ],
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site web (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.votre-clinique.fr',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            
            // Localisation
            ->add('address', TextType::class, [
                'label' => 'Adresse de la clinique *',
                'attr' => [
                    'placeholder' => '123 Rue de la Paix',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire']),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville *',
                'attr' => [
                    'placeholder' => 'Paris',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La ville est obligatoire']),
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal *',
                'attr' => [
                    'placeholder' => '75001',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le code postal est obligatoire']),
                ],
            ])
            ->add('region', ChoiceType::class, [
                'label' => 'Région *',
                'choices' => [
                    'Auvergne-Rhône-Alpes' => 'Auvergne-Rhône-Alpes',
                    'Bourgogne-Franche-Comté' => 'Bourgogne-Franche-Comté',
                    'Bretagne' => 'Bretagne',
                    'Centre-Val de Loire' => 'Centre-Val de Loire',
                    'Corse' => 'Corse',
                    'Grand Est' => 'Grand Est',
                    'Hauts-de-France' => 'Hauts-de-France',
                    'Île-de-France' => 'Île-de-France',
                    'Normandie' => 'Normandie',
                    'Nouvelle-Aquitaine' => 'Nouvelle-Aquitaine',
                    'Occitanie' => 'Occitanie',
                    'Pays de la Loire' => 'Pays de la Loire',
                    'Provence-Alpes-Côte d\'Azur' => 'Provence-Alpes-Côte d\'Azur',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La région est obligatoire']),
                ],
            ])
            
            // Formation et diplômes
            ->add('education', TextareaType::class, [
                'label' => 'Formation et diplômes *',
                'attr' => [
                    'placeholder' => 'Décrivez votre formation, diplômes, spécialisations...',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La formation est obligatoire']),
                    new Length([
                        'min' => 50,
                        'max' => 1000,
                        'minMessage' => 'La formation doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'La formation ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('graduationYear', TextType::class, [
                'label' => 'Année d\'obtention du diplôme *',
                'attr' => [
                    'placeholder' => '2015',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'année d\'obtention du diplôme est obligatoire']),
                ],
            ])
            ->add('school', TextType::class, [
                'label' => 'École vétérinaire *',
                'attr' => [
                    'placeholder' => 'École Nationale Vétérinaire de Lyon',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'école vétérinaire est obligatoire']),
                ],
            ])
            
            // Spécialités
            ->add('specialties', ChoiceType::class, [
                'label' => 'Spécialités *',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Médecine générale' => 'Médecine générale',
                    'Chirurgie' => 'Chirurgie',
                    'Dermatologie' => 'Dermatologie',
                    'Cardiologie' => 'Cardiologie',
                    'Neurologie' => 'Neurologie',
                    'Oncologie' => 'Oncologie',
                    'Ophtalmologie' => 'Ophtalmologie',
                    'Dentisterie' => 'Dentisterie',
                    'Radiologie' => 'Radiologie',
                    'Échographie' => 'Échographie',
                    'Urgences' => 'Urgences',
                    'Animaux exotiques' => 'Animaux exotiques',
                    'Équidés' => 'Équidés',
                    'Animaux de ferme' => 'Animaux de ferme',
                    'Autre' => 'Autre',
                ],
                'attr' => [
                    'class' => 'space-y-2',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner au moins une spécialité']),
                ],
            ])
            
            // Services proposés
            ->add('services', ChoiceType::class, [
                'label' => 'Services proposés *',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Consultations' => 'Consultations',
                    'Vaccinations' => 'Vaccinations',
                    'Stérilisations' => 'Stérilisations',
                    'Chirurgie' => 'Chirurgie',
                    'Radiologie' => 'Radiologie',
                    'Échographie' => 'Échographie',
                    'Analyses de laboratoire' => 'Analyses de laboratoire',
                    'Soins dentaires' => 'Soins dentaires',
                    'Urgences 24h/24' => 'Urgences 24h/24',
                    'Soins à domicile' => 'Soins à domicile',
                    'Conseils nutritionnels' => 'Conseils nutritionnels',
                    'Comportementalisme' => 'Comportementalisme',
                    'Autre' => 'Autre',
                ],
                'attr' => [
                    'class' => 'space-y-2',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner au moins un service']),
                ],
            ])
            
            // Espèces traitées
            ->add('treatedSpecies', ChoiceType::class, [
                'label' => 'Espèces traitées *',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Chiens' => 'Chiens',
                    'Chats' => 'Chats',
                    'Rongeurs' => 'Rongeurs',
                    'Oiseaux' => 'Oiseaux',
                    'Reptiles' => 'Reptiles',
                    'Équidés' => 'Équidés',
                    'Animaux de ferme' => 'Animaux de ferme',
                    'Animaux exotiques' => 'Animaux exotiques',
                    'Tous types' => 'Tous types',
                ],
                'attr' => [
                    'class' => 'space-y-2',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner au moins une espèce']),
                ],
            ])
            
            // Expérience professionnelle
            ->add('experience', ChoiceType::class, [
                'label' => 'Années d\'expérience *',
                'choices' => [
                    'Moins de 2 ans' => 'Moins de 2 ans',
                    '2-5 ans' => '2-5 ans',
                    '5-10 ans' => '5-10 ans',
                    '10-20 ans' => '10-20 ans',
                    'Plus de 20 ans' => 'Plus de 20 ans',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'expérience professionnelle est obligatoire']),
                ],
            ])
            
            // Horaires et disponibilité
            ->add('openingHours', TextareaType::class, [
                'label' => 'Horaires d\'ouverture *',
                'attr' => [
                    'placeholder' => 'Lundi-Vendredi: 9h-19h\nSamedi: 9h-17h\nDimanche: Fermé',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Les horaires d\'ouverture sont obligatoires']),
                ],
            ])
            ->add('emergencyService', CheckboxType::class, [
                'label' => 'Service d\'urgence disponible',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
            ])
            ->add('homeVisits', CheckboxType::class, [
                'label' => 'Visites à domicile',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
            ])
            
            // Tarifs et modalités
            ->add('pricing', TextareaType::class, [
                'label' => 'Informations sur les tarifs (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez vos tarifs, forfaits, modalités de paiement...',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            ->add('insurance', ChoiceType::class, [
                'label' => 'Acceptez-vous les assurances ?',
                'choices' => [
                    'Oui, toutes les assurances' => 'Oui, toutes les assurances',
                    'Oui, certaines assurances' => 'Oui, certaines assurances',
                    'Non' => 'Non',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            
            // Description et valeurs
            ->add('description', TextareaType::class, [
                'label' => 'Description de votre approche *',
                'attr' => [
                    'placeholder' => 'Décrivez votre approche, vos valeurs, votre philosophie de soins...',
                    'rows' => 4,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire']),
                    new Length([
                        'min' => 100,
                        'max' => 1500,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            
            // Références et certifications
            ->add('certifications', TextareaType::class, [
                'label' => 'Certifications et formations continues (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Listez vos certifications, formations continues, membreships...',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            ->add('references', TextareaType::class, [
                'label' => 'Références professionnelles (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom et coordonnées de confrères ou clients de référence...',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            
            // Photo
            ->add('photo', FileType::class, [
                'label' => 'Photo de profil (optionnel)',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG ou GIF)',
                        'maxSizeMessage' => 'L\'image ne peut pas dépasser 2MB',
                    ]),
                ],
            ])
            
            // Compte utilisateur
            ->add('userEmail', EmailType::class, [
                'label' => 'Email pour le compte utilisateur *',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'votre.email@exemple.com',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email pour le compte utilisateur est obligatoire']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe *',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Votre mot de passe',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe *',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Confirmez votre mot de passe',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez confirmer le mot de passe']),
                ],
            ])
            
            // Conditions
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les <a href="#" class="text-primary-600 hover:text-primary-500">conditions d\'utilisation</a> et la <a href="#" class="text-primary-600 hover:text-primary-500">politique de confidentialité</a> *',
                'label_html' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter les conditions d\'utilisation']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VetProfile::class,
        ]);
    }
}
