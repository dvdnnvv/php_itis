<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->page('home');
    }

    #[Route('/about', name: 'app_about', methods: ['GET'])]
    public function about(): Response
    {
        return $this->page('about');
    }

    #[Route('/meetings', name: 'app_meetings', methods: ['GET'])]
    public function meetings(): Response
    {
        return $this->page('meetings');
    }

    private function page(string $name): Response
    {
        $response = $this->render('page/'.$name.'.html.twig');

        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }
}
