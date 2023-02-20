<?php

namespace App\Form;

use App\Entity\ProgrammationEvenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammationEvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lieu', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Lieu de l\évènement'
            ])
            ->add('nbPlaces', NumberType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nombre de place'
            ])
            ->add('startAt', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'time_label' => 'Starts On',
            ])
            ->add('endAt', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProgrammationEvenement::class,
        ]);
    }
}
