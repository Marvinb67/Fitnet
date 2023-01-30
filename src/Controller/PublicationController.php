<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\CommentaireType;
use App\Form\SearchFormType;
use App\Form\PublicationType;
use App\Repository\UserRepository;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PublicationController extends AbstractController
{
    #[Route('/publication', name: 'app_publication')]
    public function index(PublicationRepository $publicationRepository, Request $request): Response
    {
        $user = $this->getUser();
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $data->setPage($request->get('page', 1));
        if (!$user) throw new Exception('Vous n\'êtes pas connecté(e)! ');
// dd($data);
        $publications = $publicationRepository->findSearch($data);

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/publication/new', name: 'app_publication_new')]
    public function new(
        ManagerRegistry $doctrine,
        Request $request,
        SluggerInterface $sluggerInterface
    ): Response {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) return $this->redirectToRoute('app_login');
            $publication->setUser($user);
            $publication->setSlug($sluggerInterface->slug($publication->getTitre()));
            $em = $doctrine->getManager();
            $em->persist($publication);
            $em->flush();

            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/new.html.twig', [
            'formPublication' => $form->createView(),
            'publication' => $publication
        ]);
    }

    /**
     * Affiche une publication par son id et slug
     *
     * @param Publication $publication
     * @return Response
     */
    #[Route('publication/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET', 'POST'], name: 'app_publication_show')]
    public function show(int $id, string $slug, PublicationRepository $publicationRepository, Request $request, EntityManagerInterface $em): Response
    {
        if (!$publication) {
            throw $this->createNotFoundException(
                'Cet Article n\'est exist plus!'
            );
        }

        // On crée un commentaire

        $commentaire = new Commentaire;

        // Géneration du formulaire

        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);

        $formCommentaire->handleRequest($request);

        // Traitement du fromulaire

        if($formCommentaire->isSubmitted() && $formCommentaire->isValid())
        {
            $commentaire->setUser($this->getUser());
            $commentaire->setPublication($publication);

            // Récupere le contenu du champ parentId
            $parentId = $formCommentaire->get("parentId")->getData();

            // on cherche le commentaire correspondant

            if($parentId != null){
                $parent = $em->getRepository(Commentaire::class)->find($parentId);
            }

            // On definit le parent

            $commentaire->setParent($parent ?? null);
            
            $em->persist($commentaire);
            $em->flush();

            // dd($commentaire);

            return $this->redirectToRoute('app_publication_show', [
                'slug' => $publication->getSlug(),
                'id' => $publication->getId()
            ]);
        }

        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
            'formCommentaire' => $formCommentaire->createView()
        ]);
    }

    #[Route('publication/edit/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], name: 'app_publication_edit')]
    /**
     * Modification d'une publication
     *
     * @param Publication $publication
     * @param Request $request
     * @return Response
     */
    public function edit(
        ManagerRegistry $doctrine,
        Publication $publication,
        Request $request,
    ): Response {
        if (!$publication) {
            throw $this->createNotFoundException(
                'Cet Article n\'est exist plus!'
            );
        }
        
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/edit.html.twig', [
            'publication' => $publication,
            'formPublication' => $form->createView()
        ]);
    }

    #[Route('publication/suppression/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], name: 'app_publication_delete')]
    public function delete(Publication $publication, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($publication);

        $em->flush();

        return $this->redirectToRoute('app_publication');
    }
}
