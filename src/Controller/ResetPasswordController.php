<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\ResetPasswordType;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetPasswordController extends AbstractController
{
    #[Route(path: '/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request, 
        CompteRepository $compteRepository, 
        TokenGeneratorInterface $tokenGenerator, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $compteRepository->findOneBy(['email' => $email]);

            if ($user) {
                // Génération du jeton unique
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->flush();

                // Simulation de l'envoi d'email
                $url = $this->generateUrl('app_reset_password', ['token' => $token], 0);
                $this->addFlash('success', "Lien de simulation (à copier) : <a href='$url'>$url</a>");
            } else {
                $this->addFlash('danger', "Email inconnu.");
            }
        }
        return $this->render('reset_password/request.html.twig');
    }

    #[Route(path: '/reinitialiser-mot-de-passe/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        CompteRepository $compteRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // On cherche l'utilisateur par son jeton
        $user = $compteRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Jeton invalide.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On efface le jeton et on hache le nouveau mot de passe
            $user->setResetToken(null);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour avec succès !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}