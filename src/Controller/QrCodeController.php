<?php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QrCodeController extends AbstractController
{
    #[Route('/qrcode/{token}', name: 'app_qrcode_generate')]
    public function generate(string $token): Response
    {
        // Création directe du QR Code (compatible avec toutes les versions v4/v5)
        // On passe le jeton au constructeur. La taille par défaut est de 300px.
        $qrCode = new QrCode($token);

        $writer = new PngWriter();
        
        // On génère l'image PNG
        $result = $writer->write($qrCode);

        // On renvoie la réponse avec le bon format d'image
        return new Response($result->getString(), 200, [
            'Content-Type' => 'image/png'
        ]);
    }
}