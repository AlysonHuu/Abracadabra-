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
       
        $qrCode = new QrCode($token);

        $writer = new PngWriter();
        

        $result = $writer->write($qrCode);


        return new Response($result->getString(), 200, [
            'Content-Type' => 'image/png'
        ]);
    }
}