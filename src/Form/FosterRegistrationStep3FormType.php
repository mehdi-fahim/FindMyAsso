<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FosterRegistrationStep3FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('housingType', ChoiceType::class, [
                'label' => 'Type de logement *',
                'choices' => [
                    'Appartement' => 'APARTMENT',
                    'Maison' => 'HOUSE',
                    'Ferme' => 'FARM',
                    'Autre' => 'OTHER',
                ],
                'attr' => [
                    'class' => 'form-select rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type de logement est obligatoire']),
                ],
            ])
            ->add('hasGarden', CheckboxType::class, [
                'label' => 'Avez-vous un jardin ?',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
            ])
            ->add('childrenAtHome', CheckboxType::class, [
                'label' => 'Y a-t-il des enfants à la maison ?',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
            ])
            ->add('otherPets', CheckboxType::class, [
                'label' => 'Avez-vous d\'autres animaux ?',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
            ])
            ->add('availabilityFrom', DateType::class, [
                'label' => 'Disponible à partir de (optionnel)',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            ->add('availabilityTo', DateType::class, [
                'label' => 'Disponible jusqu\'au (optionnel)',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-input rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            ->add('questionnaireAnswers', TextareaType::class, [
                'label' => 'Réponses au questionnaire détaillé',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez votre environnement, votre expérience avec les animaux, vos motivations...',
                    'rows' => 6,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
                ],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes supplémentaires (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Toute information supplémentaire que vous souhaitez partager...',
                    'rows' => 3,
                    'class' => 'form-textarea rounded-lg border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500',
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
