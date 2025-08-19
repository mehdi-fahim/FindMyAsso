<?php

namespace App\Form;

use App\Entity\FosterProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class FosterFamilyRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Informations personnelles
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet *',
                'attr' => [
                    'placeholder' => 'Votre nom et prénom',
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
            ->add('age', IntegerType::class, [
                'label' => 'Âge *',
                'attr' => [
                    'placeholder' => '25',
                    'min' => 18,
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'âge est obligatoire']),
                    new Range([
                        'min' => 18,
                        'max' => 100,
                        'minMessage' => 'Vous devez avoir au moins {{ limit }} ans',
                        'maxMessage' => 'L\'âge ne peut pas dépasser {{ limit }} ans',
                    ]),
                ],
            ])
            ->add('occupation', TextType::class, [
                'label' => 'Profession *',
                'attr' => [
                    'placeholder' => 'Votre profession actuelle',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La profession est obligatoire']),
                ],
            ])
            
            // Contact
            ->add('email', EmailType::class, [
                'label' => 'Email de contact *',
                'attr' => [
                    'placeholder' => 'votre.email@exemple.com',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone *',
                'attr' => [
                    'placeholder' => '06 12 34 56 78',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire']),
                ],
            ])
            
            // Localisation
            ->add('address', TextType::class, [
                'label' => 'Adresse *',
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
            
            // Situation familiale
            ->add('familySituation', ChoiceType::class, [
                'label' => 'Situation familiale *',
                'choices' => [
                    'Célibataire' => 'Célibataire',
                    'En couple' => 'En couple',
                    'Marié(e)' => 'Marié(e)',
                    'Divorcé(e)' => 'Divorcé(e)',
                    'Veuf/Veuve' => 'Veuf/Veuve',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La situation familiale est obligatoire']),
                ],
            ])
            ->add('childrenCount', IntegerType::class, [
                'label' => 'Nombre d\'enfants',
                'required' => false,
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 10,
                        'minMessage' => 'Le nombre d\'enfants ne peut pas être négatif',
                        'maxMessage' => 'Le nombre d\'enfants ne peut pas dépasser {{ limit }}',
                    ]),
                ],
            ])
            ->add('childrenAges', TextType::class, [
                'label' => 'Âges des enfants (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => '5, 8, 12 ans',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            
            // Expérience avec les animaux
            ->add('animalExperience', ChoiceType::class, [
                'label' => 'Expérience avec les animaux *',
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermédiaire' => 'Intermédiaire',
                    'Expérimenté' => 'Expérimenté',
                    'Professionnel' => 'Professionnel',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'expérience avec les animaux est obligatoire']),
                ],
            ])
            ->add('previousAnimals', TextareaType::class, [
                'label' => 'Animaux précédents (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez vos expériences passées avec des animaux...',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            
            // Capacités d'accueil
            ->add('acceptedSpecies', ChoiceType::class, [
                'label' => 'Espèces acceptées *',
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
                ],
                'attr' => [
                    'class' => 'space-y-2',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner au moins une espèce']),
                ],
            ])
            ->add('maxAnimals', IntegerType::class, [
                'label' => 'Nombre maximum d\'animaux *',
                'attr' => [
                    'placeholder' => '2',
                    'min' => 1,
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nombre maximum d\'animaux est obligatoire']),
                    new Range([
                        'min' => 1,
                        'max' => 10,
                        'minMessage' => 'Vous devez pouvoir accueillir au moins {{ limit }} animal',
                        'maxMessage' => 'Le nombre maximum ne peut pas dépasser {{ limit }}',
                    ]),
                ],
            ])
            ->add('specialNeeds', ChoiceType::class, [
                'label' => 'Animaux à besoins spéciaux',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'choices' => [
                    'Animaux âgés' => 'Animaux âgés',
                    'Animaux handicapés' => 'Animaux handicapés',
                    'Animaux malades' => 'Animaux malades',
                    'Femelles gestantes' => 'Femelles gestantes',
                    'Chiots/chatons' => 'Chiots/chatons',
                    'Animaux craintifs' => 'Animaux craintifs',
                    'Animaux agressifs' => 'Animaux agressifs',
                ],
                'attr' => [
                    'class' => 'space-y-2',
                ],
            ])
            
            // Logement
            ->add('housingType', ChoiceType::class, [
                'label' => 'Type de logement *',
                'choices' => [
                    'Appartement' => 'Appartement',
                    'Maison' => 'Maison',
                    'Maison avec jardin' => 'Maison avec jardin',
                    'Ferme' => 'Ferme',
                    'Autre' => 'Autre',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type de logement est obligatoire']),
                ],
            ])
            ->add('gardenSize', ChoiceType::class, [
                'label' => 'Taille du jardin (si applicable)',
                'required' => false,
                'choices' => [
                    'Petit (< 100m²)' => 'Petit (< 100m²)',
                    'Moyen (100-500m²)' => 'Moyen (100-500m²)',
                    'Grand (500-1000m²)' => 'Grand (500-1000m²)',
                    'Très grand (> 1000m²)' => 'Très grand (> 1000m²)',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            ->add('gardenFenced', CheckboxType::class, [
                'label' => 'Jardin clôturé',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
            ])
            
            // Disponibilité
            ->add('availability', ChoiceType::class, [
                'label' => 'Disponibilité *',
                'choices' => [
                    'Temps plein' => 'Temps plein',
                    'Temps partiel' => 'Temps partiel',
                    'Weekends uniquement' => 'Weekends uniquement',
                    'Vacances uniquement' => 'Vacances uniquement',
                    'Urgences uniquement' => 'Urgences uniquement',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La disponibilité est obligatoire']),
                ],
            ])
            ->add('maxDuration', ChoiceType::class, [
                'label' => 'Durée maximale d\'accueil *',
                'choices' => [
                    '1-7 jours' => '1-7 jours',
                    '1-4 semaines' => '1-4 semaines',
                    '1-3 mois' => '1-3 mois',
                    '3-6 mois' => '3-6 mois',
                    '6 mois - 1 an' => '6 mois - 1 an',
                    'Plus d\'1 an' => 'Plus d\'1 an',
                    'Adoption définitive' => 'Adoption définitive',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La durée maximale d\'accueil est obligatoire']),
                ],
            ])
            
            // Motivations
            ->add('motivation', TextareaType::class, [
                'label' => 'Motivations pour devenir famille d\'accueil *',
                'attr' => [
                    'placeholder' => 'Expliquez pourquoi vous souhaitez devenir famille d\'accueil...',
                    'rows' => 4,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Les motivations sont obligatoires']),
                    new Length([
                        'min' => 50,
                        'max' => 1000,
                        'minMessage' => 'Les motivations doivent contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Les motivations ne peuvent pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            
            // Références
            ->add('references', TextareaType::class, [
                'label' => 'Références (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom et coordonnées de personnes pouvant témoigner de votre sérieux...',
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
            'data_class' => FosterProfile::class,
        ]);
    }
}
