<?php

namespace App\Controller;

use App\Entity\Media;
use App\Data\SearchData;
use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\SearchFormType;
use App\Form\CommentaireType;
use App\Form\PublicationType;
use App\Entity\ReactionPublication;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return $this->redirectToRoute('app_login');
        }
        $publications = $publicationRepository->findSearch($data);
        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
            'form' => $form->createView(),
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
            // On récupère les images

            $images = $form->get('mediaPublication')->getData();

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
                $img->setTitre($publication->getTitre());
                $img->setSlug($sluggerInterface->slug($img->getTitre()));
                $publication->addMediaPublication($img);
            }


            $user = $this->getUser();
            if (!$user) return $this->redirectToRoute('app_login');
            $publication->setUser($user);
            $publication->setSlug($sluggerInterface->slug(strtolower($publication->getTitre())));
            $em = $doctrine->getManager();
            $em->persist($publication);
            $em->flush();

            $this->addFlash('success', 'Publication envoyé avec succès');

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
    public function show(Publication $publication, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->redirectToRoute('app_login');
        if (!$publication) throw $this->createNotFoundException('Cet Article n\'est exist plus!');

        $isPublicationLiked = $em->getRepository(ReactionPublication::class)->countByPublicationAndUser($user, $publication);
        // On crée un commentaire
        $commentaire = new Commentaire;

        // Géneration du formulaire
        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);

        $formCommentaire->handleRequest($request);

        // Traitement du fromulaire
        if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
            $commentaire->setUser($this->getUser());
            $commentaire->setPublication($publication);

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

            return $this->redirectToRoute('app_publication_show', [
                'publication' => $publication,
                'slug' => $publication->getSlug(),
                'id' => $publication->getId()
            ]);
        }

        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
            'formCommentaire' => $formCommentaire->createView(),
            'isPublicationLiked ' => $isPublicationLiked
        ]);
    }

    #[Route('publication/edit/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], name: 'app_publication_edit')]
    #[Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_USER') and publication.getUser() == user")]
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
        Request $request
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
    #[Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_USER') and publication.getUser() == user")]
    public function delete(Publication $publication, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $medias = $publication->getMediaPublication();

        if($medias)
        {
            foreach($medias as $media)
            {                
                $nomMedia = $this->getParameter('medias_directory') . '/' . $media->getLien();
                if(file_exists($nomMedia))
                {
                    unlink($nomMedia);
                }
            }
        }
        $em->remove($publication);
        
        $em->flush();

        return $this->redirectToRoute('app_publication');
    }

    #[Route('publication/like')]
    public function likePublication(EntityManagerInterface $em, SerializerInterface $serializer): Response
    {

        $user = $this->getUser();

        $publications = $em->getRepository(Publication::class)->findAll();

        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return $this->redirectToRoute('app_login');
        }

        foreach ($publications as $publication) {
            $count = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication);
            $isLikedPublication = $em->getRepository(ReactionPublication::class)->myReactionToPublication($user, $publication);
            $isReactedPublication = $em->getRepository(ReactionPublication::class)->countByPublicationAndUser($user, $publication);
            $publicationId = $publication->getId();
            $data[] = [
                'idPost' => $publicationId,
                'isExistRection' => $isReactedPublication,
                'status' => $isLikedPublication,
                'likesCount' => $count,
                // 'publication' => $serializer->normalize($publication, null, ['groups' => 'publication:read'])
            ];
        }
        // dd($data);
        return new JsonResponse(['data' => $data], Response::HTTP_OK);
    }
}
