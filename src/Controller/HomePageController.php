<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController {
  #[Route('/', name: 'cinema_homepage', methods: ['GET'])]
  public function index(): Response

{
   $films = [
        [ id => 1, 
        titre=> "Avatar 3: de feu et de cendres", 
        poster=>,
        duree=>,
        note=>, 
        seances =>

           
        ],
        [
            
        ],
    ];

 
    return $this->render('homepage.html.twig', [
        'films' => $films
    ]);


}}





?>