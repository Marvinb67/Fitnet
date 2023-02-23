<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Evenement;
use App\Entity\Commentaire;
use App\Form\EvenementType;
use App\Form\CommentaireType;
use App\Entity\ProgrammationEvenement;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgrammationEvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(ProgrammationEvenementRepository $peRepo): Response
    {
        return $this->render('evenement/index.html.twig', [
            'peRepo' => $peRepo->findBy([], ['startAt' => 'DESC']),
        ]);
    }

    #[Route('/evenement/new', name:'app_evenement_new')]
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

    #[Route('evenement/edit/{slug}', requirements: ['slug' => '[a-z0-9\-]*'], name:'app_evenement_edit')]
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

    #[Route('evenement/delete/{slug}', requirements: ['slug' => '[a-z0-9\-]*'], name: 'app_evenement_delete')]
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
