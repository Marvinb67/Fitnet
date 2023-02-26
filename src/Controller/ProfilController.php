<?php

namespace App\Controller;

use App\Entity\User;
use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted('ROLE_USER')]
#[Route('/profil')]
class ProfilController extends AbstractController
{
    /**
     * Recherche des utilisateurs
     *
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
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

            $amis = [];
            foreach ($user->getAmis() as $ami) {
                $amis[] = $ami;
            }
            $suivis = [];
            foreach ($user->getFollowUsers() as $follow) {
                $suivis[] = $follow;
            };
            
            return $this->render('profil/index.html.twig', [
                'users' => $users,
                'amis' => $amis,
                'suivis' => $suivis,

            ]);
    }
 /**
     * Géstion du formulaire de recherche des users
     *
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/search-form', name: 'search_user_form')]
    public function searchForm(UserRepository $userRepository, Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->findSearch($data);
            return $this->render('user/index.html.twig', [
                'users' => $users,
            ]);
        }
        return $this->render('_partials/_searchForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * Les relation entre utilisateurs (Amities et suivis)
     *
     * @param User $user
     * @return Response
     */
    #[Route('/{id}-{slug}', name: 'app_user_detail')]
    public function show(User $user): Response
    {
        $connectedUser = $this->getUser();
        $userProfil[] = [
            'id' => $user->getId(),
            'nom' => $user->getnom() . ' ' . $user->getprenom(),
            'image' => $user->getimage(),
            'slug' => $user->getSlug(),
            'bio' => $user->getMyProfil()->getBiographie(),
            'age' => $user->getMyProfil()->getAge(),
            'job' => $user->getMyProfil()->getJob(),
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
        
        /* foreach ($user->getFollowedByUsers() as $followedBy) {
            $followedByUsers[] = [
                'idFollowedBy' => $followedBy->getId(),
                'slug' => $followedBy->getSlug(),
                'nom' => $followedBy->getNom() . ' ' . $followedBy->getPrenom(),
            ];
        };*/

            // Les amis en commun avec l'utlisateur connecté
        $communAmis = array_intersect($user->getAmis()->toArray(), $connectedUser->getAmis()->toArray());
        foreach ($communAmis as $communAmi) {
            $amisCommun[] = $communAmi->getId();
        }
           // Les suivis en commun avec l'utlisateur connecté
        $communFollows = array_intersect($user->getFollowUsers()->toArray(), $connectedUser->getFollowUsers()->toArray());
        foreach ($communFollows as $communFollow) {
            $followsCommun[] = $communFollow->getId();
        }
        // Liste des amis de l'utlisateur connecté
        $amis = $connectedUser->getAmis()->toArray();
        foreach ($amis as $ami) {
            $amisIds[] = $ami->getId();
        }
        // Liste des suivis de l'utlisateur connecté
        $follows = $connectedUser->getFollowUsers()->toArray();
        foreach ($follows as $follow) {
            $followersIds[] = $follow->getId();
        }
        // Liste des publications de l'utilisateur consulté
        $publicationsUser = $user->getPublications()->toArray();
        // Liste des evenements de l'utilisateur consulté
        $evenementsUser = $user->getEvenements()->toArray();
        // Liste des groupes de l'utilisateur consulté
        $groupesUser = $user->getGroupes()->toArray();

        return $this->render('profil/show.html.twig', [
            'userProfil' => $userProfil,
            'amisOfAmi' => $amisOfAmi,
            'followUsers'  => $followUsers,
            // 'followedByUsers'  => $followedByUsers,
            'communAmis' => $amisCommun ?? [],
            'communFollows' => $followsCommun ?? [],
            'amisIds' => $amisIds,
            'followersIds' => $followersIds,
            'publicationsUser' => $publicationsUser,
            'evenementsUser' => $evenementsUser,
            'groupesUser' => $groupesUser,
        ]);
    }

    /**
     * Ajou/suppression d'amitier ou suivi
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/{interaction}/{etat}-{id}', name: 'app_add_interaction')]
    public function addInteraction(Request $request, UserRepository $userRepository, EntityManagerInterface $em): Response
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
        $etat = $request->get('etat');
        // En vérifier si l'utilisateur est connecté si non une reponse json exploiter au js
        $route = $request->get('interaction');
        // match est une alternative de switch(seulement php8)
        $etat = match ($etat) {
            'true' => true,
            'false' => false,
        };
        // L'utilisateur à ajouter
        $targetUser = $userRepository->findOneBy(['id' => $userId]);
        // Vérification de l'existance de l'utilisateur et de l'etat (true ou false)
        if (!$targetUser) {
            $this->addFlash('danger', 'Aucun utilisateur idenfier!');
            return $this->redirectToRoute('app_profil');
        }
        // S'assurer que l'utilisateur est déjà ami/suivi ou non
        $followedState = $user->getFollowUsers()->contains($targetUser);
        $frienshipState = $user->getAmis()->contains($targetUser);

        /**
         * Gestion des suivis
         */
        // Si le lien contient 'ajout-follow'
        if ($route === 'ajout-follow') {

            if ($followedState === true && $etat === false) {
                // Si il est déjà suivi on supprime le suivi
                $user->removeFollowUser($targetUser);
                $this->addFlash('warning', "vous ne suivez plus {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } elseif ($followedState === false && $etat === true) {
                // Si il n'est pas suivi on ajout le suivi
                $user->addFollowUser($targetUser);
                $this->addFlash('success', "vous suivez désormais {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } elseif ($followedState === true && $etat === true) {
                // Si vouloir suivi alors qu'il est déjà suivi
                $this->addFlash('warning', "Vous suiver déjà {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } elseif ($followedState === false && $etat === false) {
                // Si vouloir supprimer le suivi alors qu'il n'est existe pas
                $this->addFlash('warning', "Vous ne suivez plus {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } else {
                $this->addFlash('danger', "Probléme détecter veuillez réessayer! Merci.");
            }
        }

        /**
         * Gestion des amis
         */
        // Si le lien contient 'ajout-ami'
        elseif ($route === 'ajout-ami') {
            if ($frienshipState === true && $etat === false) {
                // Si il est déjà suivi on supprime le suivi
                $user->removeAmi($targetUser);
                $this->addFlash('warning', "vous n'êtes plus ami avec {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } elseif ($frienshipState === false && $etat === true) {
                // Si il n'est pas suivi on ajout le suivi
                $user->addAmi($targetUser);
                $this->addFlash('success', "vous êtes désormais ami avec {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } elseif ($frienshipState === true && $etat === true) {
                // Si vouloir suivi alors qu'il est déjà suivi
                $this->addFlash('warning', "Vous êtes déjà ami avec {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } elseif ($frienshipState === false && $etat === false) {
                // Si vouloir supprimer le suivi alors qu'il n'est existe pas
                $this->addFlash('warning', "Vous n'êtes pas ami avec {$targetUser->getNom()} {$targetUser->getPrenom()}!");
            } else {
                $this->addFlash('danger', "Probléme détecter veuillez réessayer! Merci.");
            }
        }
        // Enregistrement du changement dans la base des données.
        $em->flush();
        return $this->redirectToRoute('app_profil');
    }

    #[Route('/profil/suppression', name: 'app_profil_suppression')]
    #[Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_USER') and this.getUser() == user")]
    public function suppressionCompte(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        
        $session = new Session();
        $session->invalidate();

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_login');
    }
}
