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
                'realisateur' => 'Christopher Nolan',
                'genre'=>'Thriller',
                'date'=>'20 Juillet 2010',
                'duree' => 148,
                'note' => 5,
                'description' => "Dom Cobb est un voleur expérimenté dans l'art périlleux de l'extraction : sa spécialité consiste à s'approprier les secrets les plus précieux d'un individu, enfouis au plus profond de son subconscient, pendant qu'il rêve et que son esprit est particulièrement vulnérable. Très recherché pour ses talents dans l'univers trouble de l'espionnage industriel, Cobb est aussi devenu un fugitif traqué dans le monde entier. Cependant, une ultime mission pourrait lui permettre de retrouver sa vie d'avant."
            ],
            2 => [
                'titre' => 'Interstellar',
                'poster' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'realisateur' => 'Christopher Nolan',
                'genre'=>'Science-Fiction',
                'date'=>'21 Juillet 2010',
                'duree' => 169,
                'note' => 4,
                'description' => "Dans un proche futur, la Terre est devenue hostile pour l'homme. Les tempêtes de sable sont fréquentes et il n'y a plus que le maïs qui peut être cultivé, en raison d'un sol trop aride. Cooper est un pilote, recyclé en agriculteur, qui vit avec son fils et sa fille dans la ferme familiale."
            ],
            3 => [
                'titre' => 'Joker',
                'poster' => 'https://image.tmdb.org/t/p/w500/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg',
                'realisateur' => 'James bond',
                'genre'=> 'Psychologique',
                'date' =>'4 Octobre 2019',
                'duree' => 122,
                'note' => 5,
                'description' => "Arthur Fleck, comédien raté, rencontre des voyous violents en errant dans les rues de Gotham City déguisé en clown. Méprisé par la société, Fleck s'enfonce peu à peu dans la démence et devient le génie criminel connu sous le nom de Joker, un dangereux tueur psychotique."
            ],
            4 => [
                'titre' => 'The Batman',
                'poster' => 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg',
                'realisateur'=> 'Harry potter',
                'genre'=>'Fantastique',
                'date'=> '5 Août 2012',
                'duree' => 176,
                'note' => 4,
                'description' => 'Batman enquête sur une série de meurtres.'
            ],
            ['titre'=> 'Avatar 3: De Feu et de Cendre',
              'poster'=>'https://fr.web.img6.acsta.net/img/52/fb/52fb8f0345af2b0940557aa049ca19fd.jpg',
              'realisateur'=>'James Cameron',
              'genre'=>'Science-Fiction',
              'date'=>'12 Janvier 2026',
              'duree'=> 200,
              'note'=> 4,
              'description'=>"Aux prises avec le chagrin après la mort de Neteyam, la famille de Jake et Neytiri rencontre une nouvelle tribu agressive : les Na'vi. Ce peuple des cendres est dirigé par le fougueux Varang, alors que le conflit sur Pandora s'intensifie."
        
            ]
        ];

        if (!isset($films[$id])) {
            throw $this->createNotFoundException('Film non trouvé');
        }

        return $this->render('film_plus.html.twig', [
            'film' => $films[$id]
        ]);
    }
}