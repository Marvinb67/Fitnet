<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Publication;
use App\Form\SearchFormType;
use App\Form\PublicationType;
use App\Repository\UserRepository;
use App\Repository\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PublicationController extends AbstractController
{
    #[Route('/publication', name: 'app_publication')]
    public function index(PublicationRepository $publicationRepository, Request $request): Response
    {
        $user = $this->getUser();
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $data->setPage($request->get('page', 1));
        if (!$user) throw new Exception('Vous n\'êtes pas connecté(e)! ');
        
        foreach ($user->getAmis() as $ami) {
            $amis = $ami->getNom().' '.$ami->getPrenom();
            $data->setAmis($amis);
        }
// dd($data);
        $publications = $publicationRepository->findSearch($data);

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
            'form' => $form->createView()
        ]);
    }

    #[Route('/publication/new', name: 'app_publication_new')]
    public function new(
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        SluggerInterface $sluggerInterface
    ): Response {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) return $this->redirectToRoute('app_login');
            $publication->setUser($user);
            $publication->setSlug($sluggerInterface->slug($publication->getTitre()));
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

    /**
     * Affiche une publication par son id et slug
     *
     * @param int $id
     * @param string $slug
     * @param PublicationRepository $publicationRepository
     * @return Response
     */
    #[Route('publication/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], methods: ['GET'], name: 'app_publication_show')]
    public function show(int $id, string $slug, PublicationRepository $publicationRepository): Response
    {
        $publication = $publicationRepository->findOneBy(['id' => $id, 'slug' => $slug]);
        if (!$publication) {
            throw $this->createNotFoundException(
                'Cet Article n\'est exist plus!'
            );
        }

        return $this->render('publication/show.html.twig', [
            'publication' => $publication
        ]);
    }

    #[Route('publication/edit/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], name: 'app_publication_edit')]
    /**
     * Modification d'une publication
     *
     * @param string $slug
     * @param Publication $publication
     * @param PublicationRepository $repo
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function edit(
        string $slug,
        PublicationRepository $repo,
        Request $request,
        ManagerRegistry $doctrine
    ): Response {
        $publication = $repo->findOneBy(['slug' => $slug]);
        if (!$publication) {
            throw $this->createNotFoundException(
                'Cet Article n\'est exist plus!'
            );
        }
        // dd($publication);
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_publication');
        }

        return $this->render('publication/edit.html.twig', [
            'publication' => $publication,
            'formPublication' => $form->createView()
        ]);
    }

    #[Route('publication/{slug}-{id}', requirements: ['id' => '\d+', 'slug' => '[a-z0-9\-]*'], name: 'app_publication_delete')]
    public function delete(Publication $publication, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($publication);

        $em->flush();

        return $this->redirectToRoute('app_publication');
    }
}
