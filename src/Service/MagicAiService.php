<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MagicAiService
{
    public function __construct(private HttpClientInterface $client) {}

   

    public function askOracle(string $userPrompt, string $availableMovies = ""): string
    {
        if (!$userPrompt) return "Posez votre question à l'Oracle...";

       
        $instructionFilms = $availableMovies 
            ? "Tu DOIS impérativement choisir un film parmi cette liste réelle : $availableMovies." 
            : "Conseille un genre de film général.";

        try {
            $response = $this->client->request('POST', 'http://host.docker.internal:11434/api/generate', [
                'json' => [
                    'model' => 'mistral',
                    'prompt' => "Tu es M. SCHNEIDER, un oracle de cinéma. 
                                VOICI LA LISTE EXCLUSIVE DES FILMS : $availableMovies.
                                
                                CONSIGNES ABSOLUES :
                                1. Tu as L'INTERDICTION FORMELLE de proposer un film qui n'est PAS dans la liste ci-dessus.
                                2. Si l'utilisateur est fatigué, triste ou joyeux, pioche uniquement dans cette liste.
                                3. Réponds en 20 mots max : 'M. SCHNEIDER voit... [TITRE]. [EXPLICATION COURTE].'",
                    'stream' => false
                ],
                'timeout' => 120
            ]);

            return $response->toArray()['response'];
        } catch (\Exception $e) {
            return "L'Oracle est plongé dans le noir... (Erreur : " . $e->getMessage() . ")";
        }
    }

    public function analyzeAura(string $text): string
    {
        try {
            $response = $this->client->request('POST', 'http://host.docker.internal:11434/api/generate', [
                'json' => [
                    'model' => 'mistral',
                    'prompt' => "Tu es un analyseur d'émotion. Analyse ce titre : '$text'. 
                                Réponds UNIQUEMENT par l'un de ces mots, sans ponctuation : 
                                JOIE, TRISTESSE, PEUR, NOSTALGIE, MYSTERE.",
                    'stream' => false
                ],
                'timeout' => 60
            ]);

            $content = $response->toArray()['response'];
            
           
            foreach (['JOIE', 'TRISTESSE', 'PEUR', 'NOSTALGIE', 'MYSTERE'] as $key) {
                if (stripos($content, $key) !== false) return $key;
            }

            return "MYSTERE";
        } catch (\Exception $e) {
            return "MYSTERE";
        }
    }
}