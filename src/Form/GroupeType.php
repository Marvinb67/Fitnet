<?php

namespace App\Form;

use App\Entity\Groupe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom du groupe'
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/pdf',
                        ],
                        'mimeTypesMessage' => 'Format de l\'image invalide'
                    ])
                ]
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'dark-round mt-3'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
        ]);
    }
}
