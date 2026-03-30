<?php 

namespace App\Controller;

use App\Repository\FilmRepository;
use App\Service\MagicAiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OracleController extends AbstractController
{
    // 1. Route pour afficher la page instantanément
    #[Route('/oracle', name: 'app_oracle')]
    public function index(): Response
    {
        return $this->render('oracle/index.html.twig');
    }

    // 2.Route AJAX (JavaScript)
    #[Route('/oracle/ajax', name: 'app_oracle_ajax', methods: ['POST'])]
    public function ajaxAsk(Request $request, MagicAiService $aiService, FilmRepository $filmRepository): JsonResponse
    {
        // On récupère la question envoyée en JSON par JavaScript
        $data = json_decode($request->getContent(), true);
        $question = $data['question'] ?? null;

        if (!$question) {
            return new JsonResponse(['reponse' => 'L\'Oracle n\'a pas entendu votre question...']);
        }

        // On récupère tous les titres des films en base de données
        $allFilms = $filmRepository->findBy(['deletedAt' => null]);
        $titles = [];
        foreach ($allFilms as $film) {
            $titles[] = $film->getNom();
        }
        $listForAi = implode(', ', $titles);

        // On passe la question ET la liste des films à l'IA
        $reponse = $aiService->askOracle($question, $listForAi);

        // On renvoie la réponse au format JSON pour JavaScript
        return new JsonResponse(['reponse' => $reponse]);
    }
}