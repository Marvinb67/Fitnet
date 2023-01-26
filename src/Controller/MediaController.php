<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaController extends AbstractController
{
    #[Route('/media/{slug}', name: 'app_show_media')]
    public function show(Media $media): Response
    {
        return $this->render('media/show.html.twig', [
            'media' => $media,
        ]);
    }

}
