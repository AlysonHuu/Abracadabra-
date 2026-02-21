<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; 

class FilmPlusController extends AbstractController 
{ #[Route('/film/{id}',name:'film_plus')]
    public function show(int $id):Response
    { 
        $films = [
            1 => [
                'titre' => 'Inception',
                'poster' => 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
                'duree' => 148,
                'note' => 5,
                'description' => 'Un voleur infiltre les rêves pour voler des secrets.'
            ],
            2 => [
                'titre' => 'Interstellar',
                'poster' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'duree' => 169,
                'note' => 4,
                'description' => 'Une mission spatiale pour sauver l’humanité.'
            ],
            3 => [
                'titre' => 'Joker',
                'poster' => 'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg',
                'duree' => 122,
                'note' => 5,
                'description' => 'L’histoire sombre des origines du Joker.'
            ],
            4 => [
                'titre' => 'The Batman',
                'poster' => 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg',
                'duree' => 176,
                'note' => 4,
                'description' => 'Batman enquête sur une série de meurtres.'
            ],
        ];

        if (!isset($films[$id])) {
            throw $this->createNotFoundException('Film non trouvé');
        }

        return $this->render('film/film_plus.html.twig', [
            'film' => $films[$id]
        ]);
    }
}