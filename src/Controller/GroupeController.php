<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupeType;
use App\Repository\GroupeRepository;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupeController extends AbstractController
{
    #[Route('/groupe', name: 'app_groupe')]
    public function index(GroupeRepository $groupeRepository): Response
    {
        $groupes = $groupeRepository->findAll();

        return $this->render('groupe/index.html.twig', [
            'groupes' => $groupes,
        ]);
    }

    #[Route('/groupe/new', name: 'app_groupe_new')]
    public function new(EntityManagerInterface $em, Request $request, SluggerInterface $sluggerInterface): Response
    {
        $groupe = new Groupe($sluggerInterface);

        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $groupe
                ->setUser($this->getUser())
                ->setSlug($sluggerInterface->slug(strtolower($groupe->getIntitule())))
                ->addAdherentsGroupe($this->getUser())
            ;

            $em->persist($groupe);
            $em->flush();

            return $this->redirectToRoute('app_groupe');
        }

        return $this->render('/groupe/new.html.twig', [
            'formGroupe' => $form->createView()
        ]);
    }

    #[Route('/groupe/{slug}-{id}', name: 'app_groupe_show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'])]
    public function show(Groupe $groupe, PublicationRepository $publicationRepository)
    {
        $publications = $publicationRepository->findBy(['groupe' => $groupe->getId()], ['createdAt' => 'DESC']);
        return $this->render('/groupe/show.html.twig', [
            'groupe' => $groupe,
            'publications' => $publications
        ]);
    }

    #[Route('groupe/edit/{slug}', requirements: ['slug' => '[a-z0-9\-]*'], name:'app_groupe_edit')]
    #[Security("is_granted('ROLE_USER') or is_granted('ROLE_SUPER_ADMIN') and groupe.getUser() == user")]
    public function edit(Groupe $groupe, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(GroupeType::class, $groupe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            return $this->redirectToRoute('app_groupe');
        }

        return $this->render('groupe/edit.html.twig', [
            'groupe' => $groupe,
            'formGroupe' => $form->createView()
        ]);
    }

    #[Route('groupe/delete/{slug}', requirements: ['slug' => '[a-z0-9\-]*'], name: 'app_groupe_delete')]
    public function delete(Groupe $groupe, EntityManagerInterface $em)
    {
        $em->remove($groupe);
        $em->flush();

        return $this->redirectToRoute('app_groupe');
    }

    #[Route('/groupe/rejoindre/{id}', name: 'app_groupe_rejoindre')]
    public function rejoindre(Groupe $groupe, Request $request, EntityManagerInterface $em): Response
    {
        $this->getUser()->addMesgroupe($groupe);
        $em->flush();

        return $this->redirectToRoute('app_groupe_show', [
            'slug' => $groupe->getSlug(),
            'id' => $groupe->getId()
        ]);
    }

    #[Route('/groupe/desinscription/{id}', 'app_groupe_desinscription')]
    public function desinscription(Groupe $groupe, Request $request, EntityManagerInterface $em)
    {
        $this->getUser()->removeMesgroupe($groupe);
        $em->flush();

        return $this->redirectToRoute('app_groupe');
    }
}
