<?php

namespace App\Form;

use App\Entity\AdoptionRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class AdoptionRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom *',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Length(['min' => 2, 'max' => 50, 'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères', 'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères']),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                    new Length(['min' => 2, 'max' => 50, 'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères', 'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères']),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est obligatoire']),
                    new Email(['message' => 'L\'email n\'est pas valide']),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone *',
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire']),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse *',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse est obligatoire']),
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal *',
                'constraints' => [
                    new NotBlank(['message' => 'Le code postal est obligatoire']),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville *',
                'constraints' => [
                    new NotBlank(['message' => 'La ville est obligatoire']),
                ],
            ])
            ->add('region', ChoiceType::class, [
                'label' => 'Région *',
                'choices' => [
                    'Auvergne-Rhône-Alpes' => 'auvergne-rhone-alpes',
                    'Bourgogne-Franche-Comté' => 'bourgogne-franche-comte',
                    'Bretagne' => 'bretagne',
                    'Centre-Val de Loire' => 'centre-val-de-loire',
                    'Corse' => 'corse',
                    'Grand Est' => 'grand-est',
                    'Hauts-de-France' => 'hauts-de-france',
                    'Île-de-France' => 'ile-de-france',
                    'Normandie' => 'normandie',
                    'Nouvelle-Aquitaine' => 'nouvelle-aquitaine',
                    'Occitanie' => 'occitanie',
                    'Pays de la Loire' => 'pays-de-la-loire',
                    'Provence-Alpes-Côte d\'Azur' => 'provence-alpes-cote-azur',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La région est obligatoire']),
                ],
            ])
            ->add('homeType', ChoiceType::class, [
                'label' => 'Type de logement *',
                'choices' => [
                    'Appartement' => 'appartement',
                    'Maison' => 'maison',
                    'Pavillon' => 'pavillon',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type de logement est obligatoire']),
                ],
            ])
            ->add('garden', ChoiceType::class, [
                'label' => 'Jardin/Extérieur',
                'choices' => [
                    'Oui, jardin clôturé' => 'oui',
                    'Oui, partiellement clôturé' => 'partiel',
                    'Non' => 'non',
                ],
                'required' => false,
            ])
            ->add('otherAnimals', TextareaType::class, [
                'label' => 'Autres animaux à la maison',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Décrivez les autres animaux présents dans votre foyer...',
                ],
            ])
            ->add('children', ChoiceType::class, [
                'label' => 'Enfants à la maison',
                'choices' => [
                    'Aucun' => 'aucun',
                    '0-3 ans' => '0-3',
                    '4-12 ans' => '4-12',
                    '13+ ans' => '13+',
                ],
                'required' => false,
            ])
            ->add('experience', ChoiceType::class, [
                'label' => 'Expérience avec les animaux',
                'choices' => [
                    'Débutant' => 'debutant',
                    'Intermédiaire' => 'intermediaire',
                    'Expert' => 'expert',
                ],
                'required' => false,
            ])
            ->add('motivation', TextareaType::class, [
                'label' => 'Motivation pour l\'adoption *',
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Expliquez pourquoi vous souhaitez adopter cet animal...',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La motivation est obligatoire']),
                    new Length(['min' => 50, 'minMessage' => 'La motivation doit contenir au moins {{ limit }} caractères']),
                ],
            ])
            ->add('lifestyle', TextareaType::class, [
                'label' => 'Mode de vie et disponibilité *',
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Décrivez votre mode de vie, vos horaires, votre disponibilité...',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La description du mode de vie est obligatoire']),
                ],
            ])
            ->add('budget', ChoiceType::class, [
                'label' => 'Budget mensuel pour l\'animal *',
                'choices' => [
                    'Moins de 50€' => 'moins_50',
                    '50€ - 100€' => '50_100',
                    '100€ - 200€' => '100_200',
                    'Plus de 200€' => 'plus_200',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le budget est obligatoire']),
                ],
            ])
            ->add('consent', CheckboxType::class, [
                'label' => 'J\'accepte que mes informations soient utilisées pour traiter ma demande d\'adoption et je consens à être contacté(e) par l\'association. *',
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez accepter les conditions']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdoptionRequest::class,
        ]);
    }
}
