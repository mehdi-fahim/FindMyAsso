<?php

namespace App\Form;

use App\Entity\Association;
use App\Entity\InKindDonation;
use App\Repository\AssociationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class InKindDonationFormType extends AbstractType
{
    public function __construct(
        private AssociationRepository $associationRepository
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de don',
                'choices' => [
                    'Nourriture' => 'food',
                    'Jouets' => 'toys',
                    'Équipement' => 'equipment',
                    'Médicaments' => 'medicine',
                    'Autre' => 'other',
                ],
                'attr' => [
                    'class' => 'form-select rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un type de don',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr' => [
                    'placeholder' => 'Décrivez ce que vous souhaitez donner...',
                    'rows' => 4,
                    'class' => 'form-textarea rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez décrire votre don',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 1000,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('quantity', TextType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'placeholder' => 'ex: 5 boîtes, 2 sacs, 1 lot...',
                    'class' => 'form-input rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez indiquer la quantité',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'La quantité ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('region', ChoiceType::class, [
                'label' => 'Région',
                'choices' => [
                    'Île-de-France' => 'Île-de-France',
                    'Auvergne-Rhône-Alpes' => 'Auvergne-Rhône-Alpes',
                    'Occitanie' => 'Occitanie',
                    'Nouvelle-Aquitaine' => 'Nouvelle-Aquitaine',
                    'Hauts-de-France' => 'Hauts-de-France',
                    'Grand Est' => 'Grand Est',
                    'Bourgogne-Franche-Comté' => 'Bourgogne-Franche-Comté',
                    'Centre-Val de Loire' => 'Centre-Val de Loire',
                    'Pays de la Loire' => 'Pays de la Loire',
                    'Bretagne' => 'Bretagne',
                    'Normandie' => 'Normandie',
                    'Provence-Alpes-Côte d\'Azur' => 'Provence-Alpes-Côte d\'Azur',
                    'Corse' => 'Corse',
                ],
                'attr' => [
                    'class' => 'form-select rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une région',
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Votre ville',
                    'class' => 'form-input rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre ville',
                    ]),
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de la ville ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('association', EntityType::class, [
                'label' => 'Association (optionnel)',
                'class' => Association::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Choisir une association spécifique',
                'query_builder' => function (AssociationRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.isApproved = :approved')
                        ->setParameter('approved', true)
                        ->orderBy('a.name', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes supplémentaires (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Informations complémentaires, conditions de retrait...',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Les notes ne peuvent pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InKindDonation::class,
        ]);
    }
}
