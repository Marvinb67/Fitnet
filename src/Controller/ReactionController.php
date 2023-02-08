<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\ReactionPublication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reaction', name: 'app_reaction_stats', methods: ['GET'])]
class ReactionController extends AbstractController
{
    #[Route('/stats')]
    public function index(EntityManagerInterface $em, PublicationRepository $publicationRepository): Response
    {
        $publications = $publicationRepository->findBy(['isActive' => true]);
        foreach ($publications as $publication) {
            $countLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 1);
            $countDisLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 0);
            $likes[] = [
                "idPublication" => $publication->getId(),
                "countLikes" => $countLikes,
                "countDisLikes" => $countDisLikes
            ];
        }
        return new JsonResponse(['likes' => $likes]);
    }

    #[Route('/like/{idPublication}-{etatLikeDislike}', name: 'app_reaction', methods: ['GET', 'POST'])]
    public function new(int $idPublication, string $etatLikeDislike, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return new JsonResponse(
                Response::HTTP_BAD_GATEWAY
            );
        }

        $idPublication = $request->get('idPublication');
        $etatLikeDislike = $request->get('etatLikeDislike');
        $etat = match ($etatLikeDislike) {
            'true' => true,
            'false' => false,
        };
        $publication = $em->getRepository(Publication::class)->findOneBy(['id' => $idPublication]);
        $reaction = $em->getRepository(ReactionPublication::class)->findOneBy(['user' => $user, 'publication' => $publication]);

        $manager = $doctrine->getManager();

        $reactionPublication = new ReactionPublication();
        $reactionPublication->setUser($user);
        $reactionPublication->setPublication($publication);

        if (!empty($reaction)) {
            if ($reaction->isEtatLikeDislike() === $etat) {
                $message = $reaction->isEtatLikeDislike() ? 'Vous n\'aimez plus cette publication!' : 'Vous ne détestez plus cette publication!';
                $manager->remove($reaction);
                $manager->flush();
                $isReactedPublication = $em->getRepository(ReactionPublication::class)->countByPublicationAndUser($user, $publication);
                $countLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 1);
                $countDisLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 0);
                return new JsonResponse([
                    'message' => $message,
                    'countLikes' => $countLikes,
                    'countDisLikes' => $countDisLikes,
                    'reaction' => $isReactedPublication
                ], Response::HTTP_OK);
            } else {
                $manager->remove($reaction);
            }
        }
        $reactionPublication->setEtatLikeDislike($etat);
        $manager->persist($reactionPublication);
        $this->addFlash('sucess', 'Votre réaction est enregistrer!');
        $manager->flush();

        $isReactedPublication = $em->getRepository(ReactionPublication::class)->countByPublicationAndUser($user, $publication);
        $countLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 1);
        $countDisLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 0);
        return new JsonResponse(
            [
                'message' => '',
                'countLikes' => $countLikes,
                'countDisLikes' => $countDisLikes,
                'reaction' => $isReactedPublication
            ],
            Response::HTTP_OK
        );
    }
}
