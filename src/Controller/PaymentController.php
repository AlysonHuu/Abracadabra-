<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/seance/{id}/paiement', name: 'app_payment_checkout', methods: ['POST'])]
    public function checkout(Seance $seance, Request $request): Response
    {
        // On initialise Stripe avec ta clé secrète du .env
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $nbPlaces = $request->request->get('nb_places', 1);
        $prixUnitaire = 1000; // 10.00€ (Stripe compte en centimes)

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Réservation : ' . $seance->getFilm()->getNom()],
                    'unit_amount' => $prixUnitaire,
                ],
                'quantity' => $nbPlaces,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_success', [
                'id' => $seance->getId(),
                'nb' => $nbPlaces
            ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_seance_show', ['id' => $seance->getId()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/paiement/succes/{id}/{nb}', name: 'app_payment_success')]
    public function success(Seance $seance, int $nb, EntityManagerInterface $em): Response
    {
        // On crée la réservation
        $reservation = new Reservation();
        $reservation->setCompte($this->getUser());
        $reservation->setSeance($seance);
        $reservation->setNbPlaces($nb);
        $reservation->setCreatedAt(new \DateTimeImmutable());

        // On met à jour le remplissage de la salle
        $seance->setNbPlaceReservees($seance->getNbPlaceReservees() + $nb);

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Réservation enregistrée !');
        return $this->redirectToRoute('app_mon_compte');
    }
}