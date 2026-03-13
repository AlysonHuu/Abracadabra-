<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\FilmRepository;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'cinema_homepage')]
public function index(FilmRepository $filmRepository): Response
{
    // On récupère tous les films de la base de données (Dispo et non-supprimés) 
    $filmsEnBase = $filmRepository->findBy([
        'estDispo' => true,
        'deletedAt' => null 
    ]);
    return $this->render('homepage.html.twig', [
        'films' => $filmsEnBase,
    ]);
}


}