<?php 

namespace App\Controller;

use App\Repository\FilmRepository; // Importe ton repository de films
use App\Service\MagicAiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OracleController extends AbstractController
{
    #[Route('/oracle', name: 'app_oracle')]
    public function index(Request $request, MagicAiService $aiService, FilmRepository $filmRepository): Response
    {
        $question = $request->query->get('q');
        $reponse = null;

        if ($question) {
            // 1. On récupère tous les titres des films en base de données
            $allFilms = $filmRepository->findBy(['deletedAt' => null]);
            $titles = [];
            foreach ($allFilms as $film) {
                $titles[] = $film->getNom();
            }
            $listForAi = implode(', ', $titles);

            // 2. On passe la question ET la liste des films à l'IA
            $reponse = $aiService->askOracle($question, $listForAi);
        }

        return $this->render('oracle/index.html.twig', [
            'reponse' => $reponse,
            'question' => $question
        ]);
    }
}