<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir un email valid'
                ])
            ],
            'required' => true,
            'attr' => [
                'class' => 'form-control',
            ]
        ])
        ->add('nom', TextType::class, [
            'attr' => [
                'class' => 'form-control',
            ]
        ])
        ->add('prenom', TextType::class, [
            'attr' => [
                'class' => 'form-control',
            ]
        ])
            ->add('image', FileType::class, [
                'label' => 'Photo',
                // mapped' => false n'est pas lier à aucune proprité d'entité
                'mapped' => false,
                // Comme ça on est pas obliger de retélécharger l'image à chaque edition.
                'required' => false,
                // unmapped champs on ne peut pas définir leurs validations on utilisant les annotations
                // dans l'entity associe, pour ça qu'on utilise PHP constraint classes
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/git',
                        ],
                        'mimeTypesMessage' => 'Inserez une extension valide. Seulement(.png, .jpg, .jpeg, ou .git), maximum 1024Ko',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
