<?php

namespace App\Controller;

use App\Form\UpdateCompteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route; // Import identique à SecurityController
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    #[Route(path: '/mon-compte', name: 'app_mon_compte')]
    public function monCompte(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $user = $this->getUser();
        
        // Sécurité : redirection si non connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UpdateCompteType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            // Note : updatedAt est géré automatiquement par l'entité Compte
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour !');
            return $this->redirectToRoute('app_mon_compte');
        }

        return $this->render('user/mon_compte.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route(path: '/mon-compte/supprimer', name: 'app_mon_compte_delete', methods: ['POST'])]
    public function deleteAccount(
        Request $request, 
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ): Response {
        $user = $this->getUser();
        
        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            // Soft Delete : on met à jour la date de suppression
            $user->setDeletedAt(new \DateTimeImmutable());
            $entityManager->flush();

            // Déconnexion manuelle
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            $this->addFlash('info', 'Votre compte a bien été supprimé.');
            return $this->redirectToRoute('cinema_homepage');
        }

        return $this->redirectToRoute('app_mon_compte');
    }
}