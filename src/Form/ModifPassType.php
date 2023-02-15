<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ModifPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actuelPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Ancien Mot de Passe',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => ['label' => 'Nouveau Mot de passe','attr' => [
                    'class' => 'form-control'
                ],],
                'second_options' => ['label' => 'Confirmation du mot de passe','attr' => [
                    'class' => 'form-control'
                ],],
                'constraints' => new Length([
                    'min' => 9,
                    'minMessage' => "Veuillez mettre plus de {{ limit }} characters",
                    'max' => 82,
                    'maxMessage' => "Veuillez ne pas mettre plus de {{ limit }} characters",
                    
                ]),
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label'=>'Valider',
                 'attr'=>[
                    'class'=>'btn btn-primary form-control my-2 profile-button'
                 ]
                ]);
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true,
        ]);
    }
}
