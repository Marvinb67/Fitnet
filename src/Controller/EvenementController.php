<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EvenementController extends AbstractController
{
    #[Route('/evenement', name: 'app_evenement')]
    public function index(EvenementRepository $evenementRepository): Response
    {
        $evenements = $evenementRepository->findAll();

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    #[Route('/evenement/new', name:'app_evenement_new')]
    public function new(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $evenement->setUser($this->getUser());
            $evenement->setSlug(strtolower($slugger->slug($evenement->getIntitule())));
            $em = $doctrine->getManager();
            $em->persist($evenement);
            $em->flush();

            return $this->redirectToRoute('app_evenement');
        }

        return $this->render('evenement/new.html.twig', [
            'formEvenement' => $form->createView(),
            'evenement' => $evenement,
        ]);
    }

    #[Route('evenement/{slug}',requirements: ['slug' => '[a-z0-9\-]*'], name:'app_evenement_show')]
    public function show(Evenement $evenement)
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement
        ]);
    }

    #[Route('evenement/{id}/edit', requirements: ['id' => '\d+'], name:'app_evenement_edit')]
    public function edit(Evenement $evenement, Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_evenement');
        }

        return $this->render('evenement/edit.html.twig', [
            'publictaion' => $evenement,
            'formEvenement' => $form->createView()
        ]);
    }

    #[Route('evenement/{id}/delete', name: 'app_evenement_delete')]
    public function delete(Evenement $evenement, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $em->remove($evenement);
        $em->flush();

        return $this->redirectToRoute('app_evenement');
    }

}
