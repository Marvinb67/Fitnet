<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Titre'
            ])
            ->add('contenu', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Contenu'
            ])
            // ->add('createdAt')
            // ->add('editedAt')
            // ->add('slug')
            // ->add('user')
            // ->add('mediaPublication')
            // ->add('tagsPublication')
            ->add('envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary btn-lg mt-3'
                ],
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
