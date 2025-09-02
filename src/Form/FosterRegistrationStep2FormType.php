<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class FosterRegistrationStep2FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('capacity', IntegerType::class, [
                'label' => 'Capacité d\'accueil *',
                'attr' => [
                    'placeholder' => '2',
                    'min' => 1,
                    'max' => 10,
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La capacité d\'accueil est obligatoire']),
                    new Range([
                        'min' => 1,
                        'max' => 10,
                        'minMessage' => 'Vous devez pouvoir accueillir au moins {{ limit }} animal',
                        'maxMessage' => 'La capacité ne peut pas dépasser {{ limit }}',
                    ]),
                ],
            ])
            ->add('speciesAccepted', ChoiceType::class, [
                'label' => 'Espèces acceptées *',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Chiens' => 'DOG',
                    'Chats' => 'CAT',
                    'Autres' => 'OTHER',
                ],
                'attr' => [
                    'class' => 'space-y-2',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner au moins une espèce']),
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
            ->add('department', TextType::class, [
                'label' => 'Département *',
                'attr' => [
                    'placeholder' => 'Paris',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le département est obligatoire']),
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
            ->add('street', TextType::class, [
                'label' => 'Adresse *',
                'attr' => [
                    'placeholder' => '123 Rue de la Paix',
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
