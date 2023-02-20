<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Publication;
use App\Form\SearchFormType;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]

    public function index(PublicationRepository $publicationRepository, Request $request): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $data = new SearchData();

        $data->setPage($request->get('page', 1));

        $publications = $publicationRepository->findSearch($data);
        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    #[Route('/search-form', name: 'search_form')]
    public function searchForm(PublicationRepository $publicationRepository, Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data->setTag($request->get('tag'));
            dd($data);
            $publications = $publicationRepository->findSearch($data);
            return $this->render('publication/index.html.twig', [
                'publications' => $publications,
            ]);
        }
        return $this->render('_partials/_searchForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
