<?php

namespace App\Form;

use App\Entity\User;
use App\Data\SearchData;
use App\Entity\Publication;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            // ->add('amis', EntityType::class, [
            //     'label' => false,
            //     'class' => User::class,
            //     // 'query_builder' =>  function (EntityRepository $er) {
            //     //     return $er->createQueryBuilder('u')
            //     //         ->select('CONCAT(u.nom, " ", u.prenom) as ami')
            //     //         ->leftJoin('u.id', 'ami.id')
            //     //         ->where('u.id = *LOGGED USER ID*')
            //     //         ;
            //     // },
            //     // function(){
            //     //     $user = $this->getUser();
            //     //     foreach ($user->getAmis() as $ami) {
            //     //         $amis = $ami->getNom().' '.$ami->getPrenom();
            //     //         $amis;
            //     //     }
            //     // },
            //     'required' => false,
            //     'expanded' => true,
            //     'multiple' => true,
            // ])
            // ->add('dates', EntityType::class, [
            //     'label' => false,
            //     'required' => false,
            //     'class' => Publication::class,
            //     'expanded' => true,
            //     'multiple' => true,
            //     'attr' => [
            //         'class' => 'js-datepicker',
            //     ],
            // ])
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
