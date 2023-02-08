<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MobileDetectBundle\DeviceDetector\MobileDetectorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PublicationRepository $publicationRepository, Request $request, MobileDetectorInterface $mobileDetector): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $publications = $publicationRepository->findSearch($data);
        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
            'form' => $form->createView(),
            'is_mobile' => $mobileDetector->isMobile(),
            'is_tablet' => $mobileDetector->isTablet(),
            'is_iphone' => $mobileDetector->is('iPhone')
        ]);
    }
}
