<?php

namespace App\Controller;

use App\Entity\User;
use App\Data\SearchData;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('ROLE_USER')]
#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return $this->redirectToRoute('app_login');
        }
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $data->setPage($request->get('page', 1));
        $users = $userRepository->findSearch($data);
        foreach ($user->getAmis() as $ami) {
            $amis[] = $ami;
        }
        foreach ($user->getFollowUsers() as $follow) {
            $suivis[] = $follow;
        };

        return $this->render('profil/index.html.twig', [
            'users' => $users,
            'amis' => $amis,
            'suivis' => $suivis,

        ]);
    }

    #[Route('/{id}-{slug}', name: 'app_user_detail')]
    public function show(User $user): Response
    {
        $connectedUser = $this->getUser();
        $userProfil[] = [
            'id' => $user->getId(),
            'nom' => $user->getnom() . ' ' . $user->getprenom(),
            'image' => $user->getimage(),
            'slug' => $user->getSlug(),
        ];

        foreach ($user->getAmis() as $ami) {
            $amisOfAmi[] = [
                'idAmi' => $ami->getId(),
                'slug' => $ami->getSlug(),
                'image' => $ami->getimage(),
                'nom' => $ami->getNom() . ' ' . $ami->getPrenom(),
            ];
        };
        foreach ($user->getFollowUsers() as $follow) {
            $followUsers[] = [
                'idFollowedUser' => $follow->getId(),
                'slug' => $follow->getSlug(),
                'nom' => $follow->getNom() . ' ' . $follow->getPrenom(),
                'image' => $follow->getimage(),
            ];
        };
        // foreach ($user->getFollowedByUsers() as $followedBy) {
        //     $followedByUsers[] = [
        //         'idFollowedBy' => $followedBy->getId(),
        //         'slug' => $followedBy->getSlug(),
        //         'nom' => $followedBy->getNom() . ' ' . $followedBy->getPrenom(),
        //     ];
        // };

        $communAmis = array_intersect($user->getAmis()->toArray(), $connectedUser->getAmis()->toArray());
        foreach ($communAmis as $communAmi) {
            $amisCommun[] = $communAmi->getId();
        }

        $communFollows = array_intersect($user->getFollowUsers()->toArray(), $connectedUser->getFollowUsers()->toArray());
        foreach ($communFollows as $communFollow) {
            $followsCommun[] = $communFollow->getId();
        }

        $amis = $connectedUser->getAmis()->toArray();
        foreach ($amis as $ami) {
            $amisIds[] = $ami->getId();
        }
        $follows = $connectedUser->getFollowUsers()->toArray();
        foreach ($follows as $follow) {
            $followersIds[] = $follow->getId();
        }
        return $this->render('profil/show.html.twig', [
            'userProfil' => $userProfil,
            'amisOfAmi' => $amisOfAmi,
            'followUsers'  => $followUsers,
            // 'followedByUsers'  => $followedByUsers,
            'communAmis' => $amisCommun?? [],
            'communFollows' => $followsCommun?? [],
            'amisIds' => $amisIds,
            'followersIds' => $followersIds,
        ]);
    }

    #[Route('/ajout-ami/{frendship}-{id}', name: 'app_add_friend')]
    public function addFriend(int $id, User $user, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $ami = $userRepository->findOneBy(['id' => $id]);
        $userAmi = $user->addAmi($ami);
        $em->persist($userAmi);
        $em->flush();
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/ajout-follow/{followEtat}-{id}', name: 'app_add_follow')]
    public function addFollow(Request $request, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Redirection vers page de connexion si aucun utilisateur connecté
        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return $this->redirectToRoute('app_login');
        }
        // Récupération de l'id de l'utlisateur à suivre et l'état du suivi.
        $userId = $request->get('id');
        // En vérifier si l'utilisateur est connecté si non une reponse json exploiter au js
        $followEtat = $request->get('followEtat');
        // match est une alternative de switch(seulement php8)
        $etat = match ($followEtat) {
            'true' => true,
            'false' => false,
        };
        // L'utilisateur à ajouter
        $followedUser = $userRepository->findOneBy(['id' => $userId]);
        // Vérification de l'existance de l'utilisateur et de l'etat (true ou false)
        if (!$followedUser) {
            $this->addFlash('danger', 'Aucun utilisateur idenfier!');
            return $this->redirectToRoute('app_profil');
        }
        // S'assurer que l'utilisateur est déjà suivi ou non
        $followedState = $user->getFollowUsers()->contains($followedUser);

        if ($followedState === true && $etat === false) {
            // Si il est déjà suivi on supprime le suivi
            $user->removeFollowUser($followedUser);
            $this->addFlash('warning', "vous ne suivez plus {$followedUser->getNom()} {$followedUser->getPrenom()}!");
        } elseif ($followedState === false && $etat === true) {
            // Si il n'est pas suivi on ajout le suivi
            $user->addFollowUser($followedUser);
            $this->addFlash('success', "vous suivez désormais {$followedUser->getNom()} {$followedUser->getPrenom()}!");
        }elseif ($followedState === true && $etat === true) {
            // Si vouloir suivi alors qu'il est déjà suivi
            $this->addFlash('warning', "Vous suiver déjà {$followedUser->getNom()} {$followedUser->getPrenom()}!");
        }elseif ($followedState === false && $etat === false) {
            // Si vouloir supprimer le suivi alors qu'il n'est existe pas
            $this->addFlash('warning', "Vous ne suivez plus {$followedUser->getNom()} {$followedUser->getPrenom()}!");
        }
        else {
            $this->addFlash('danger', "Probléme détecter veuillez réessayer! Merci.");
        }
        // Enregistrement du changement dans la base des données.
        $em->flush();
        return $this->redirectToRoute('app_profil');
    }
}
