<?php

namespace App\Controller;

use App\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', 'home')]
    public function home(): string
    {
        return $this->twig->render('index/home.html.twig');
    }

    #[Route('/contact', 'contact')]
    public function contact(): string
    {
        return $this->twig->render('index/contact.html.twig');
    }
}
