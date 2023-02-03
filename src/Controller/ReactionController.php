<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\ReactionPublication;
use App\Form\ReactionPublicationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReactionPublicationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reaction')]
class ReactionController extends AbstractController
{
    #[Route('/', name: 'app_reaction_index', methods: ['GET'])]
    public function index(ReactionPublicationRepository $reactionPublicationRepository): Response
    {
        return $this->render('reaction/index.html.twig', [
            'reaction_publications' => $reactionPublicationRepository->findAll(),
        ]);
    }

    #[Route('/like/{idPublication}-{etatLikeDislike}', name: 'app_reaction_new', methods: ['GET', 'POST'])]
    public function new(int $idPublication, string $etatLikeDislike, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return $this->redirectToRoute('app_login');
        }

        $idPublication = $request->get('idPublication');
        $etatLikeDislike = $request->get('etatLikeDislike');
        $etat = match ($etatLikeDislike) {
            'true' => true,
            'false' => false,
        };
        $publication = $em->getRepository(Publication::class)->findOneBy(['id' => $idPublication]);
        $reaction = $em->getRepository(ReactionPublication::class)->findOneBy(['user' => $user, 'publication' => $publication]);
        dd($reaction->isEtatLikeDislike());
        $manager = $doctrine->getManager();
        if (empty($reaction)) {
            $reactionPublication = new ReactionPublication();
            $reactionPublication->setUser($user);
            $reactionPublication->setPublication($publication);
            $reactionPublication->setEtatLikeDislike($etat);
            $manager->persist($reactionPublication);
            $this->addFlash('sucess', 'Votre réaction est enregistrer!');
        } 
        else if ($etat == $reaction->isEtatLikeDislike()){
            $manager->remove($reaction);
        }    
        else {
            $reactionPublication = new ReactionPublication();
            $reactionPublication->setUser($user);
            $reactionPublication->setPublication($publication);
            $reactionPublication->setEtatLikeDislike($etat);
            $manager->persist($reactionPublication);
            $this->addFlash('sucess', 'Votre réaction est enregistrer!');
        }
        

        $manager->flush();

        $isReactedPublication = $em->getRepository(ReactionPublication::class)->countByPublicationAndUser($user, $publication);
        $count = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication);
        return new JsonResponse(
            [
                'count' => $count,
                'reaction' => $isReactedPublication
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'app_reaction_show', methods: ['GET'])]
    public function show(ReactionPublication $reactionPublication): Response
    {
        return $this->render('reaction/show.html.twig', [
            'reaction_publication' => $reactionPublication,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReactionPublication $reactionPublication, ReactionPublicationRepository $reactionPublicationRepository): Response
    {
        $form = $this->createForm(ReactionPublicationType::class, $reactionPublication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reactionPublicationRepository->save($reactionPublication, true);

            return $this->redirectToRoute('app_reaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reaction/edit.html.twig', [
            'reaction_publication' => $reactionPublication,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reaction_delete', methods: ['POST'])]
    public function delete(Request $request, ReactionPublication $reactionPublication, ReactionPublicationRepository $reactionPublicationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reactionPublication->getId(), $request->request->get('_token'))) {
            $reactionPublicationRepository->remove($reactionPublication, true);
        }

        return $this->redirectToRoute('app_reaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
