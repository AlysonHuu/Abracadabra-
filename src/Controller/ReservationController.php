<?php
namespace App\Controller;

use App\Entity\BonFidelite;
use App\Entity\Reservation;
use App\Entity\Seance;
use App\Repository\ReservationRepository;
use App\Service\FideliteService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservation/nouvelle/{id}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Seance $seance,
        Request $request,
        FideliteService $fidelite
    ): Response {
        $prixUnitaire = 10.0;
        /** @var \App\Entity\Compte $user */
        $user = $this->getUser();
        
        
        

        if ($request->isMethod('POST')) {
            $nbPlaces = (int) $request->request->get('nb_places');
            $codeBon  = trim((string) $request->request->get('code_bon', ''));

            if ($nbPlaces > 0) {
                $calcul = $fidelite->calculerPrix($user, $prixUnitaire, $codeBon ?: null);

               
                if ($calcul['bon_applique']) {
                    return $this->redirectToRoute('app_reservation_success', [
                        'id'       => $seance->getId(),
                        'nb'       => $nbPlaces,
                        'code_bon' => $codeBon,
                    ]);
                }

                
                Stripe::setApiKey($this->getParameter('stripe_secret_key'));

                $unitCents   = (int) round($calcul['prix'] * 100);
                $description = 'Film : ' . $seance->getFilm()->getNom();
                if ($calcul['reduction_jeune']) {
                    $description .= ' (Tarif Jeune -40%)';
                }

                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items'           => [[
                        'price_data' => [
                            'currency'     => 'eur',
                            'product_data' => ['name' => $description],
                            'unit_amount'  => $unitCents,
                        ],
                        'quantity' => $nbPlaces,
                    ]],
                    'mode'        => 'payment',
                    'success_url' => $this->generateUrl(
                        'app_reservation_success',
                        ['id' => $seance->getId(), 'nb' => $nbPlaces],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'cancel_url'  => $this->generateUrl(
                        'app_reservation_new',
                        ['id' => $seance->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ]);

                return $this->redirect($session->url, 303);
            }
        }

        
        $calcul          = $fidelite->calculerPrix($user, $prixUnitaire);
        $bonsDisponibles = $user->getBonsFideliteDisponibles();

        return $this->render('reservation/new.html.twig', [
            'seance'          => $seance,
            'prixUnitaire'    => $prixUnitaire,
            'prixFinal'       => $calcul['prix'],
            'reductionJeune'  => $calcul['reduction_jeune'],
            'bonsDisponibles' => $bonsDisponibles,
        ]);
    }

    #[Route('/reservation/success/{id}/{nb}', name: 'app_reservation_success')]
    #[IsGranted('ROLE_USER')]
    public function success(
        Seance $seance,
        int $nb,
        Request $request,
        EntityManagerInterface $em,
        FideliteService $fidelite
    ): Response {
        /** @var \App\Entity\Compte $user */
        $user       = $this->getUser();
        $prixUnitaire = 10.0;
        $codeBon    = $request->query->get('code_bon');
        $isGratuite = false;

        $calcul = $fidelite->calculerPrix($user, $prixUnitaire, $codeBon);

 
        if ($calcul['bon_applique']) {
            $bon = $em->getRepository(BonFidelite::class)->findByCode($codeBon);
            if ($bon) {
                $fidelite->utiliserBon($bon);
            }
            $isGratuite = true;
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setSeance($seance);
        $reservation->setNbPlaces($nb);
        $reservation->setPrixTotal($nb * $calcul['prix']);
        $reservation->setGratuite($isGratuite);
        $reservation->setTicketToken(bin2hex(random_bytes(16)));

        $seance->setNbPlaceReservees($seance->getNbPlaceReservees() + $nb);
        $em->persist($reservation);
        $em->flush();

        if (!$isGratuite) {
            $nouveauBon = $fidelite->verifierEtAttribuerBon($user);
            if ($nouveauBon) {
                $this->addFlash('success',
                    '🎉 Félicitations ! Vous avez atteint 5 réservations. '
                    . 'Votre bon offert : <strong>' . $nouveauBon->getCode() . '</strong>'
                );
            }
        }

        $this->addFlash('success', 'Paiement réussi ! Votre billet magique est prêt. ✨');
        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/mes-reservations', name: 'app_reservation_index')]
    public function index(ReservationRepository $repository): Response
    {
        $reservations = $repository->findBy(['user' => $this->getUser()]);
        return $this->render('reservation/index.html.twig', ['reservations' => $reservations]);
    }
}