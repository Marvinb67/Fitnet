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
    #[Route('/home', name: 'home')]
    public function index(PublicationRepository $publicationRepository, Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $publications = $publicationRepository->findSearch($data);

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
            'form' => $form->createView(),

        ]);
    }
}
