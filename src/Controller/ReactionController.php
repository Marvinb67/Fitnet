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
    /**
     * Permet d'avoir les statistiques des like/dislike de chaque publication et de les transmettre en json pour les exploiter en ajax
     *
     * @param EntityManagerInterface $em
     * @param PublicationRepository $publicationRepository
     * @return Response
     */
    #[Route('/stats', name: 'app_reactions_stats', methods: ['GET'])]
    public function index(EntityManagerInterface $em, PublicationRepository $publicationRepository): Response
    {
        // Récupération des publications
        $publications = $publicationRepository->findBy(['isActive' => true]);
        foreach ($publications as $publication) {
            // Compte des likes
            $countLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 1);
            // Compte des dislikes
            $countDisLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 0);
            //  Stock les résultat avec l'id de la publication
            $likes[] = [
                "idPublication" => $publication->getId(),
                "countLikes" => $countLikes,
                "countDisLikes" => $countDisLikes
            ];
        }
        // Return les résultat en json pour les exploiter en js
        return new JsonResponse(['likes' => $likes], Response::HTTP_OK);
    }

    /**
     * Permet la vérification l'existance ou pas d'une réaction puis la création ou la suppression
     * like si le lien contient {etatLikeDislike}==true ou dislike si c'est {etatLikeDislike}==false
     * 
     * @param integer $idPublication
     * @param string $etatLikeDislike
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/like/{idPublication}-{etatLikeDislike}', name: 'app_reactions', methods: ['GET', 'POST'])]
    public function new(int $idPublication, string $etatLikeDislike, Request $request, ManagerRegistry $doctrine, EntityManagerInterface $em): Response
    {
        // En vérifier si l'utilisateur est connecté si non une reponse json exploiter au js
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return new JsonResponse(
                Response::HTTP_BAD_GATEWAY
            );
        }
        // Récupération de l'id de la publication et la réaction souhaité
        $idPublication = $request->get('idPublication');
        $etatLikeDislike = $request->get('etatLikeDislike');
        // match est une alternative de switch(seulement php8)
        $etat = match ($etatLikeDislike) {
            'true' => true,
            'false' => false,
        };
        // Récupération à partir de la base des données de la publication et l'etat de la reaction de l'utilisateur à celle-ci
        $publication = $em->getRepository(Publication::class)->findOneBy(['id' => $idPublication]);
        $reaction = $em->getRepository(ReactionPublication::class)->findOneBy(['user' => $user, 'publication' => $publication]);

        $manager = $doctrine->getManager();
        // Iniciation de la création d'une nouvelle réaction
        $reactionPublication = new ReactionPublication();
        $reactionPublication->setUser($user);
        $reactionPublication->setPublication($publication);

        // Si une réaction trouvé (dans la BDD)
        if (!empty($reaction)) {
            // Vérification si la réaction existant est la même que celle envoyer dans l'url dans ce cas en supprime la réaction.
            if ($reaction->isEtatLikeDislike() === $etat) {
                $message = $reaction->isEtatLikeDislike() ? 'Vous n\'aimez plus cette publication!' : 'Vous ne détestez plus cette publication!';
                $manager->remove($reaction);
                $manager->flush();
                // Retour de donnée a exploiter en js
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
                // Et si la réaction existant n'est pas la même que celle envoyer dans l'url dans ce cas on supprime la réaction et on continue
                $manager->remove($reaction);
            }
        }
        // On continue la création de la nouvelle réaction
        $reactionPublication->setEtatLikeDislike($etat);
        $manager->persist($reactionPublication);
        $this->addFlash('sucess', 'Votre réaction est enregistrer!');
        $manager->flush();

        // Retour de donnée a exploiter en js
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

    #[Route('/stats/{id}', name: 'app_reactions_pub_stats', methods: ['GET'])]
    public function show(EntityManagerInterface $em, Publication $publication): Response
    {
            // Compte des likes
            $countLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 1);
            // Compte des dislikes
            $countDisLikes = $em->getRepository(ReactionPublication::class)->countByPublicationLikes($publication, 0);
            //  Stock les résultat avec l'id de la publication
            $likes[] = [
                'message' => '',
                "idPublication" => $publication->getId(),
                "countLikes" => $countLikes,
                "countDisLikes" => $countDisLikes
            ];
        // Return les résultat en json pour les exploiter en js
        return new JsonResponse(['likes' => $likes], Response::HTTP_OK);

    }
}
