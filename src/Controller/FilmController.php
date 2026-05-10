<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Repository\FilmRepository;
use App\Service\MagicAiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/film')]
final class FilmController extends AbstractController
{
    #[Route('/recherche', name: 'app_film_search', methods: ['GET'])]
    public function search(Request $request, FilmRepository $filmRepository): Response
    {
        $query = $request->query->get('q');
        $films = [];

        if ($query) {
            
            $films = $filmRepository->createQueryBuilder('f')
                ->where('f.Nom LIKE :q')
                ->andWhere('f.deletedAt IS NULL')
                ->andWhere('f.createdAt IS NOT NULL')
                ->setParameter('q', '%' . $query . '%')
                ->orderBy('f.Nom', 'ASC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('film/search_results.html.twig', [
            'films' => $films,
            'query' => $query
        ]);
    }

    #[Route('/', name: 'app_film_index', methods: ['GET'])]
    public function index(FilmRepository $filmRepository): Response
    {
        return $this->render('film/index.html.twig', [
            'films' => $filmRepository->findBy(['deletedAt' => null]),
        ]);
    }

    #[Route('/new', name: 'app_film_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, MagicAiService $aiService): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('Affiche')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('affiches_directory'), $newFilename);
                $film->setAffiche($newFilename);
            }

          
            $film->setAura($aiService->analyzeAura($film->getNom()));

            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('film/new.html.twig', ['film' => $film, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_film_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Film $film): Response
    {
        $seancesParJour = [];
        foreach ($film->getSeances() as $seance) {
            $dateKey = $seance->getDateDiffusion()->format('Y-m-d');
            $seancesParJour[$dateKey][] = $seance;
        }
        ksort($seancesParJour);

        return $this->render('film/show.html.twig', [
            'film' => $film,
            'seancesParJour' => $seancesParJour,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_film_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Film $film, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $ancienneAffiche = $film->getAffiche();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('Affiche')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('affiches_directory'), $newFilename);
                if ($ancienneAffiche && file_exists($this->getParameter('affiches_directory').'/'.$ancienneAffiche)) {
                    unlink($this->getParameter('affiches_directory').'/'.$ancienneAffiche);
                }
                $film->setAffiche($newFilename);
            } else {
                $film->setAffiche($ancienneAffiche);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('film/edit.html.twig', ['film' => $film, 'form' => $form]);
    }

    #[Route('/{id}', name: 'app_film_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Film $film, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$film->getId(), $request->getPayload()->getString('_token'))) {
            $film->setDeletedAt(new \DateTimeImmutable());
            $film->setEstDispo(false); 
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_film_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/rattrapage-auras', name: 'app_film_rattrapage', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function rattrapageAuras(FilmRepository $repo, MagicAiService $aiService, EntityManagerInterface $em): Response
    {
        $films = $repo->findAll();
        $compteur = 0;
        foreach ($films as $film) {
            if (!$film->getAura()) {
                $film->setAura($aiService->analyzeAura($film->getNom()));
                $compteur++;
            }
        }
        $em->flush();
        return new Response("Magie terminée ! $compteur films mis à jour.");
    }
}