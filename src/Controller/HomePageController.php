<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'home')] // Attribute PHP 8 . Définit la route principale (home) du site. Execution de la méthode index()
public function index(): Response 
{
    $films = [
        [
            'titre' => 'Inception',
            'poster' => 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
            'duree' => 148,
            'note' => 5
        ],
        [
            'titre' => 'Interstellar',
            'poster' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
            'duree' => 169,
            'note' => 4
        ],
        [
            'titre' => 'Joker',
            'poster' => 'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg',
            'duree' => 122,
            'note' => 5
        ],
        [
            'titre' => 'The Batman',
            'poster' => 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg',
            'duree' => 176,
            'note' => 4
        ]
    ];

    return $this->render('homepage.html.twig', [ //méthode render avec twig qui gere l'affichage et le controleur les données 
        'films' => $films
    ]);
}


}