<?php

namespace App\Controller;

use App\Entity\User;
use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\UserRepository;
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
        // dd($users);
        return $this->render('profil/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/{slug}', name: 'app_user_detail')]
    public function show(User $user): Response
    {
        $ConnectedUser = $this->getUser();

        if ($ConnectedUser === $user || $this->isGranted('ROLE_ADMIN')) {
            // $currentUser = $user->getId();
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
                    'id' => $user->getId(),
                    'amis' => $amis,
                    'followUsers'  => $followUsers,
                    'followedByUsers'  => $followedByUsers
                ]
            );
        }
        return new JsonResponse(['test']);
    }
}
