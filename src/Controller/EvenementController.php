<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Evenement;
use App\Entity\Commentaire;
use App\Form\EvenementType;
use App\Form\CommentaireType;
use App\Entity\ProgrammationEvenement;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgrammationEvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvenementController extends AbstractController
{
    /**
     * Affiche la liste des événements.
     *
     * @param ProgrammationEvenementRepository $peRepo Le repository pour les programmations d'événements.
     * @return Response La réponse HTTP contenant la vue Twig.
    */
    #[Route('/evenement', name: 'app_evenement')]
    public function index(ProgrammationEvenementRepository $peRepo) : Response
    {
        return $this->render('evenement/index.html.twig', [
            'peRepo' => $peRepo->findBy([], ['startAt' => 'DESC']),

        ]);
    }

    /**
     * Affiche le formulaire pour créer un nouvel événement.
     *
     * @param ManagerRegistry $doctrine Le gestionnaire de doctrine.
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param SluggerInterface $slugger Le slugger pour générer les slugs.
     * @return Response La réponse HTTP contenant la vue Twig.
    */
    #[Route('/evenement/new', name:'app_evenement_new')]
    #[Security("is_granted('ROLE_USER')")]
    public function new(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $evenement->setUser($this->getUser());
            $evenement->setSlug(strtolower($slugger->slug($evenement->getIntitule())));
            foreach ($evenement->getHistoriqueEvenements() as $historiqueEvenement)
            {
                $historiqueEvenement->setEvenement($evenement);
            }

             // On récupère les images
             $images = $form->get('mediaEvenement')->getData();
             foreach ($images as $image) {
                 //On génère un nouveau nom de fichier
                 $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                 //On copie e fichier dans le dossier upload
                 $image->move(
                     $this->getParameter('medias_directory'),
                     $fichier
                 );
                 //On stocke l'image dans la base de données
                 $img = new Media();
                 $img->setLien($fichier);
                 $img->setTitre($evenement->getIntitule());
                 $img->setSlug($slugger->slug($img->getTitre()));
                 $evenement->addMediaEvenement($img);
            }
            
            $em = $doctrine->getManager();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute('app_evenement');
        }

        return $this->render('evenement/new.html.twig', [
            'formEvenement' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }

    /**
     * Affiche les détails d'un événement.
     *
     * @param Evenement $evenement L'événement à afficher.
     * @param ProgrammationEvenementRepository $peRepo Le repository pour les programmations d'événements.
     * @param ProgrammationEvenementRepository $events Le repository pour les événements.
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param EntityManagerInterface $em L'EntityManager pour persister les commentaires.
     * @return Response La réponse HTTP contenant la vue Twig.
    */
    #[Route('evenement/{slug}-{id}',requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET', 'POST'], name:'app_evenement_show')]
    public function show(Evenement $evenement, ProgrammationEvenement $peRepo, ProgrammationEvenementRepository $events, Request $request, EntityManagerInterface $em)
    {
        // On crée un commentaire
        $commentaire = new Commentaire;
        // Géneration du formulaire
        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);

        $formCommentaire->handleRequest($request);

        // Traitement du fromulaire
        if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
            $commentaire->setUser($this->getUser());
            $commentaire->setEvenement($evenement);

            // Récupere le contenu du champ parentId
            $parentId = $formCommentaire->get("parentId")->getData();

            // on cherche le commentaire correspondant

            if ($parentId != null) {
                $parent = $em->getRepository(Commentaire::class)->find($parentId);
            }

            // On definit le parent

            $commentaire->setParent($parent ?? null);

            $em->persist($commentaire);
            $em->flush();

            // dd($commentaire);

            return $this->redirectToRoute('app_evenement_show', [
                'evenement' => $evenement,
                'slug' => $evenement->getSlug(),
                'id' => $evenement->getId()
            ]);
        }

        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
            'peRepo' => $peRepo,
            'events' => $events->findAll(),
            'formCommentaire' => $formCommentaire->createView()
        ]);
    }

    /**
     * Affiche le formulaire pour modifier un événement existant.
     *
     * @param Evenement $evenement L'événement à modifier.
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param ManagerRegistry $doctrine Le gestionnaire de doctrine.
     * @return Response La réponse HTTP contenant la vue Twig.
    */
    #[Route('evenement/edit/{slug}', requirements: ['slug' => '[a-z0-9\-]*'], name:'app_evenement_edit')]
    #[Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_USER') and evenement.getUser() == user")]
    public function edit(Evenement $evenement, Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_evenement');
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'formEvenement' => $form->createView()
        ]);
    }

    /**
     * Supprime un événement et tous les médias associés.
     *
     * @param Evenement $evenement L'événement à supprimer.
     * @param ManagerRegistry $doctrine Le registre de gestionnaires d'entités.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse La réponse de redirection vers la liste des événements.
    */
    #[Route('evenement/delete/{slug}', requirements: ['slug' => '[a-z0-9\-]*'], name: 'app_evenement_delete')]
    #[Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_USER') and evenement.getUser() == user")]
    public function delete(Evenement $evenement, ManagerRegistry $doctrine)
    {
        $medias = $evenement->getMediaEvenement();

        if($medias)
        {
            foreach($medias as $media)
            {                
                $nomMedia = $this->getParameter('medias_directory').'/'.$media->getLien();
                if(file_exists($nomMedia))
                {
                    unlink($nomMedia);
                }
            }
        }

        $em = $doctrine->getManager();
        $em->remove($evenement);
        $em->flush();

        return $this->redirectToRoute('app_evenement');
    }

}
