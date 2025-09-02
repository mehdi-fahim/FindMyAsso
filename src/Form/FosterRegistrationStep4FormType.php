<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class FosterRegistrationStep4FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isVisible', CheckboxType::class, [
                'label' => 'Rendre mon profil visible aux associations',
                'required' => false,
                'attr' => [
                    'class' => 'form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded',
                ],
            ])
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
            'data_class' => null,
        ]);
    }
}
