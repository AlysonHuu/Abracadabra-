<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Form\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/seance')]
final class SeanceController extends AbstractController
{
    #[Route(name: 'app_seance_index', methods: ['GET'])]
    public function index(SeanceRepository $seanceRepository): Response
    {
        return $this->render('seance/index.html.twig', [
            'seances' => $seanceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_seance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // RÉCUPÉRATION ET FUSION
            $datePart = $form->get('date_part')->getData(); // ex: 2026-03-26
            $timePart = $form->get('time_part')->getData(); // ex: 14:30:00

            if ($datePart && $timePart) {
                $dateTime = new \DateTime();
                $dateTime->setDate($datePart->format('Y'), $datePart->format('m'), $datePart->format('d'));
                $dateTime->setTime($timePart->format('H'), $timePart->format('i'));
                $seance->setDateDiffusion($dateTime);
            }

            $entityManager->persist($seance);
            $entityManager->flush();

            return $this->redirectToRoute('app_seance_index');
        }

        return $this->render('seance/new.html.twig', [
            'seance' => $seance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seance_show', methods: ['GET'])]
    public function show(Seance $seance): Response
    {
        return $this->render('seance/show.html.twig', [
            'seance' => $seance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeanceType::class, $seance);

        if ($seance->getDateDiffusion()) {
            $form->get('date_part')->setData($seance->getDateDiffusion());
            $form->get('time_part')->setData($seance->getDateDiffusion());
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // FUSION des deux champs vers la propriété réelle
            $datePart = $form->get('date_part')->getData();
            $timePart = $form->get('time_part')->getData();

            if ($datePart && $timePart) {
                $dateTime = $seance->getDateDiffusion();
                $dateTime->setDate($datePart->format('Y'), $datePart->format('m'), $datePart->format('d'));
                $dateTime->setTime($timePart->format('H'), $timePart->format('i'));
                $seance->setDateDiffusion($dateTime);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seance/edit.html.twig', [
            'seance' => $seance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seance_delete', methods: ['POST'])]
    public function delete(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seance->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($seance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
    }
}
