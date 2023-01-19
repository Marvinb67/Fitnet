<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\UserRepository;
use App\Repository\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PublicationController extends AbstractController
{
    #[Route('/publication', name: 'app_publication')]
    public function index(PublicationRepository $publicationRepository): Response
    {
        $publications = $publicationRepository->findAll();

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    #[Route('/publication/new', name:'app_publication_new')]
    public function new(ManagerRegistry $doctrine, Request $request, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $randomKey = array_rand($users);

        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $publication
                ->setUser($users[$randomKey])
                ->setSlug('slug')
            ;
            $em = $doctrine->getManager();
            $em->persist($publication);
            $em->flush();

            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/new.html.twig', [
            'formPublication' => $form->createView(),
            'publication' => $publication
        ]);
    }

    #[Route('publication/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], name:'app_publication_show')]
    public function show(Publication $publication): Response
    {
        return $this->render('publication/show.html.twig',[
            'publication' => $publication
        ]);
    }

    #[Route('publication/{id}/edit', name:'app_publication_edit')]
    #[Security("is_granted('ROLE_SUPER_ADMIN' or (is_granted('ROLE_USER') and publication.getUser() == user")]
    public function edit(Publication $publication, Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/edit.html.twig', [
            'publictaion' => $publication,
            'formPublication' => $form->createView()
        ]);
    }

    #[Route('publication/{id}', name:'app_publication_delete')]
    #[Security("is_granted('ROLE_SUPER_ADMIN') or (is_granted('ROLE_USER') and publication.getUser() == user")]
    public function delete(Publication $publication, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($publication);

        $em->flush();

        return $this->redirectToRoute('app_publication');
    }

}
