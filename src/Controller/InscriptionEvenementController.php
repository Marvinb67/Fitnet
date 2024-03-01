<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\ProgrammationEvenement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionEvenementController extends AbstractController
{
    #[Route('/inscription/evenement', name: 'app_inscription_evenement')]
    public function index(): Response
    {
        return $this->render('inscription_evenement/index.html.twig', [
            'controller_name' => 'InscriptionEvenementController',
        ]);
    }

    #[Route('/inscription/evenement/add/{id}', name: 'app_inscription_evenement_add')]
    public function inscription(EntityManagerInterface $em, ProgrammationEvenement $pe): Response
    {
        $this->getUser()->addProgrammationEvenement($pe);

        $em->flush();

        return $this->redirectToRoute('app_evenement');

    }

    #[Route('/inscription/evenement/remove/{id}', name: 'app_inscription_evenement_remove')]
    public function desinscription(EntityManagerInterface $em, ProgrammationEvenement $pe): Response
    {
        $this->getUser()->removeProgrammationEvenement($pe);

        $em->flush();

        return $this->redirectToRoute('app_evenement');
    }
}
