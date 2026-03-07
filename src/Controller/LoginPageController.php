<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginPageController extends AbstractController
{
    // C'est ici qu'on définit l'URL. Quand tu taperas /login, Symfony exécutera cette fonction.
    #[Route('/login', name: 'login')]
    public function index(): Response
    {
        // On dit à Symfony d'afficher le fichier loginpage.html.twig
        return $this->render('loginpage.html.twig');
    }
}