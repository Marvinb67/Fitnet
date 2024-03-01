<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passes doivent etre identiques!',
                'options' => [
                    'attr' => [
                        'class' => 'form-control authentification-input mt-3',
                        'autocomplete' => 'new-password'
                        ]
                    ],
                'mapped' => true,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe'),
                'constraints' => new Length([
                    'min' => 8,
                    'minMessage' => "Veuillez mettre plus de {{ limit }} characters",
                    'max' => 82,
                    'maxMessage' => "Veuillez ne pas mettre plus de {{ limit }} characters",
    
                ]),
                'required' => true
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
