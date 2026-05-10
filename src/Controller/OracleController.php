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
   
    #[Route('/oracle', name: 'app_oracle')]
    public function index(): Response
    {
        return $this->render('oracle/index.html.twig');
    }


    #[Route('/oracle/ajax', name: 'app_oracle_ajax', methods: ['POST'])]
    public function ajaxAsk(Request $request, MagicAiService $aiService, FilmRepository $filmRepository): JsonResponse
    {
      
        $data = json_decode($request->getContent(), true);
        $question = $data['question'] ?? null;

        if (!$question) {
            return new JsonResponse(['reponse' => 'L\'Oracle n\'a pas entendu votre question...']);
        }

       
        $allFilms = $filmRepository->findBy(['deletedAt' => null]);
        $titles = [];
        foreach ($allFilms as $film) {
            $titles[] = $film->getNom();
        }
        $listForAi = implode(', ', $titles);

 
        $reponse = $aiService->askOracle($question, $listForAi);

    
        return new JsonResponse(['reponse' => $reponse]);
    }
}