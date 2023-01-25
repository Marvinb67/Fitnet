<?php

namespace App\Form;

use App\Entity\User;
use App\Data\SearchData;
use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher',
                    'class' => 'form-control'
                ]
            ])
            ->add('amis', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('dates', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Publication::class,
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'js-datepicker',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'Get',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '' ;
    }
}
