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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ReservationRepository;

class ReservationController extends AbstractController
{
    #[Route('/reservation/nouvelle/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Seance $seance, Request $request): Response
    {
        $prixUnitaire = 10.0;

        if ($request->isMethod('POST')) {
            $nbPlaces = (int) $request->request->get('nb_places');

            if ($nbPlaces > 0) {
                // Initialisation de Stripe avec ta clé secrète du .env
                Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => 'Film : ' . $seance->getFilm()->getNom(),
                            ],
                            'unit_amount' => $prixUnitaire * 100, 
                        ],
                        'quantity' => $nbPlaces,
                    ]],
                    'mode' => 'payment',
                    'success_url' => $this->generateUrl('app_reservation_success', [
                        'id' => $seance->getId(),
                        'nb' => $nbPlaces
                    ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
                    'cancel_url' => $this->generateUrl('app_reservation_new', ['id' => $seance->getId()], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
                ]);

                return $this->redirect($session->url, 303);
            }
        }

        return $this->render('reservation/new.html.twig', [
            'seance' => $seance,
            'prixUnitaire' => $prixUnitaire
        ]);
    }

    #[Route('/reservation/success/{id}/{nb}', name: 'app_reservation_success')]
    #[IsGranted('ROLE_USER')]
    public function success(Seance $seance, int $nb, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $prixUnitaire = 10.0;

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setSeance($seance);
        $reservation->setNbPlaces($nb);
        $reservation->setPrixTotal($nb * $prixUnitaire);

        // --- LA CORRECTION EST ICI ---
        // Génération d'un jeton unique pour le QR Code (32 caractères aléatoires)
        $token = bin2hex(random_bytes(16));
        $reservation->setTicketToken($token);
        // ------------------------------

        // Mise à jour du compteur de places de la séance
        $seance->setNbPlaceReservees($seance->getNbPlaceReservees() + $nb);

        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', 'Paiement réussi ! Votre billet magique est prêt.');

        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/mes-reservations', name: 'app_reservation_index')]
    public function index(ReservationRepository $repository): Response
    {
        // On récupère les réservations de l'utilisateur connecté
        return $this->render('reservation/index.html.twig', [
            'reservations' => $repository->findBy(['user' => $this->getUser()])
        ]);
    }


}   