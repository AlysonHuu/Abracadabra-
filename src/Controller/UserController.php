<?php
namespace App\Controller;

use App\Entity\Compte;
use App\Form\UpdateCompteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    #[Route(path: '/mon-compte', name: 'app_mon_compte')]
    public function monCompte(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        
        /** @var Compte $user */
        $user = $entityManager->getRepository(Compte::class)->find($this->getUser()->getId());

        $form = $this->createForm(UpdateCompteType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

        

            

            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour !');
            return $this->redirectToRoute('app_mon_compte');
        }

        return $this->render('user/mon_compte.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/mon-compte/supprimer', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        Request $request
    ): Response {
        /** @var Compte $user */
        $user = $entityManager->getRepository(Compte::class)->find($this->getUser()->getId());

        $tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été supprimé avec succès. Au plaisir de vous revoir !');
        return $this->redirectToRoute('app_accueil');
    }
}