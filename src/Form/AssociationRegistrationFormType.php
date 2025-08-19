<?php

namespace App\Form;

use App\Entity\Association;
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

class AssociationRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Informations de base
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'association *',
                'attr' => [
                    'placeholder' => 'Nom de votre association',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de l\'association est obligatoire']),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'association *',
                'attr' => [
                    'placeholder' => 'Décrivez votre association, ses missions, ses valeurs...',
                    'rows' => 4,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire']),
                    new Length([
                        'min' => 50,
                        'max' => 2000,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            
            // Contact
            ->add('email', EmailType::class, [
                'label' => 'Email de contact *',
                'attr' => [
                    'placeholder' => 'contact@votre-association.fr',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone *',
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
                    'placeholder' => 'https://www.votre-association.fr',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
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
            
            // Spécialités
            ->add('specialties', ChoiceType::class, [
                'label' => 'Spécialités *',
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
                    new NotBlank(['message' => 'Veuillez sélectionner au moins une spécialité']),
                ],
            ])
            
            // Capacités
            ->add('maxCapacity', TextType::class, [
                'label' => 'Capacité maximale d\'accueil *',
                'attr' => [
                    'placeholder' => '50',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La capacité maximale est obligatoire']),
                ],
            ])
            
            // Logo
            ->add('logo', FileType::class, [
                'label' => 'Logo de l\'association (optionnel)',
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
            
            // Informations supplémentaires
            ->add('foundingDate', TextType::class, [
                'label' => 'Date de création (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => '2010',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            ->add('legalStatus', ChoiceType::class, [
                'label' => 'Statut juridique *',
                'choices' => [
                    'Association loi 1901' => 'Association loi 1901',
                    'Fondation' => 'Fondation',
                    'ONG' => 'ONG',
                    'Autre' => 'Autre',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le statut juridique est obligatoire']),
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
            'data_class' => Association::class,
        ]);
    }
}
