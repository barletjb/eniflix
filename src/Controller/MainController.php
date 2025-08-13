<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('main/home.html.twig');
    }
}
