<?php

namespace App\Form;


use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchFormType extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher',
                    'class' => 'form-control search-input'
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
    // private function detectUri()
    // {
    //     $request = new Request();
    //     $uri = $request->getUri();
    //     $route = $uri === '/profil/' ? $this->urlGenerator->generate("search_user_form") : $this->urlGenerator->generate("search_form");
    //     // dd($route);
    //     return $route;
    // }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'get',
            'action' => $this->urlGenerator->generate("search_form"),
            // on définit l'action qui doit traiter le formulaire. Si cette option n'est pas renseignée, le formulaire sera traité par la page en cours, ce qui n'est pas ce que l'on souhaite (tu peux essayer d'enlever cette option et envoyer le formulaire pour voir)
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
