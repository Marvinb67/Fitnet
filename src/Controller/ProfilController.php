<?php

namespace App\Controller;

use App\Entity\User;
use App\Data\SearchData;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil')]
    public function index(UserRepository $userRepository, Request $request): Response
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
        $users = $userRepository->findSearch($data);
        foreach ($user->getAmis() as $ami) {
            $amis[] = $ami;
        }
        foreach ($user->getFollowUsers() as $follow) {
            $suivis[] = $follow;
        };
        // dd($amis);
        return $this->render('profil/index.html.twig', [
            'users' => $users,
            'amis' => $amis,
            'suivis' => $suivis,

        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{slug}', name: 'app_user_detail')]
    public function show(User $user): Response
    {
        $ConnectedUser = $this->getUser();

            // $currentUser = $user->getId();
            $userProfil[] = [
                'id' => $user->getId(),
                'nom' => $user->getnom().' '.$user->getprenom(),
                'image' => $user->getimage(),
                'slug' => $user->getSlug(),
            ];
            
            foreach ($user->getAmis() as $ami) {
                $amis[] = [
                    'idAmi' => $ami->getId(),
                    'slug' => $ami->getSlug(),
                    'nom' => $ami->getNom() . ' ' . $ami->getPrenom(),
                ];
            };
            foreach ($user->getFollowUsers() as $follow) {
                $followUsers[] = [
                    'idFollowedUser' => $follow->getId(),
                    'slug' => $follow->getSlug(),
                    'nom' => $follow->getNom() . ' ' . $follow->getPrenom(),
                ];
            };
            foreach ($user->getFollowedByUsers() as $followedBy) {
                $followedByUsers[] = [
                    'idFollowedBy' => $followedBy->getId(),
                    'slug' => $followedBy->getSlug(),
                    'nom' => $followedBy->getNom() . ' ' . $followedBy->getPrenom(),
                ];
            };
            return new JsonResponse(
                [
                    'userProfil' => $userProfil,
                    'amis' => $amis,
                    'followUsers'  => $followUsers,
                    'followedByUsers'  => $followedByUsers
                ]
            );

        // return new JsonResponse(['test']);
    }

    #[Route('/ajout-ami/{slug}{id}', name: 'app_add_friend')]
    public function addFriend(int $id, User $user, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        if (!$user) {
            $this->addFlash('danger', 'vous n\'êtes pas connecté!');
            return $this->redirectToRoute('app_login');
        }
        $ami = $userRepository->findOneBy(['id'=>$id]);
        $userAmi = $user->addAmi($ami);
        $em->persist($userAmi);
        $em->flush();
        return $this->redirectToRoute('app_profil');
    }
}
