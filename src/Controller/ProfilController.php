<?php

namespace App\Controller;

use App\Entity\User;
use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_users')]
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

    #[Route('/{slug}', name: 'app_user_detail')]
    public function show(User $user): Response
    {
        $ConnectedUser = $this->getUser();
        if ($ConnectedUser === $user) {
            // $currentUser = $user->getId();
            return new JsonResponse(['id' => $user->getId()]);
        }
        return new JsonResponse(['id' => $user->getId()]);

    }
}
