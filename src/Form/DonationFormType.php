<?php

namespace App\Form;

use App\Entity\Association;
use App\Entity\Donation;
use App\Repository\AssociationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class DonationFormType extends AbstractType
{
    public function __construct(
        private AssociationRepository $associationRepository
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', IntegerType::class, [
                'label' => 'Montant du don (€)',
                'attr' => [
                    'placeholder' => '50',
                    'class' => 'form-input rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                    'min' => 1,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un montant',
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le montant doit être supérieur à 0',
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 10000,
                        'minMessage' => 'Le montant minimum est de {{ limit }}€',
                        'maxMessage' => 'Le montant maximum est de {{ limit }}€',
                    ]),
                ],
            ])
            ->add('currency', ChoiceType::class, [
                'label' => 'Devise',
                'choices' => [
                    'EUR (€)' => 'EUR',
                    'USD ($)' => 'USD',
                    'GBP (£)' => 'GBP',
                ],
                'data' => 'EUR',
                'attr' => [
                    'class' => 'form-select rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
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
            ->add('message', TextareaType::class, [
                'label' => 'Message (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre message de soutien...',
                    'rows' => 4,
                    'class' => 'form-textarea rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500',
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Length([
                        'max' => 1000,
                        'maxMessage' => 'Votre message ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
        ]);
    }
}
