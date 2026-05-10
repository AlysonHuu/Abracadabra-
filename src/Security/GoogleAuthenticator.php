<?php

namespace App\Security;

use App\Entity\Compte;
use League\OAuth2\Client\Provider\GoogleUser;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends OAuth2Authenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router
    ) {}

    /**
     * Cette méthode détecte si la requête doit être gérée par cet authentificateur.
     */
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    /**
     * Récupère les données de Google et prépare le passeport de sécurité.
     */
    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

           
                $user = $this->entityManager->getRepository(Compte::class)->findOneBy(['email' => $email]);

                if (!$user) {
                    $user = new Compte();
                    $user->setEmail($email);
                    $user->setNom($googleUser->getLastName() ?? 'Utilisateur');
                    $user->setPrenom($googleUser->getFirstName() ?? 'Google');
                  
                    $user->setPassword(bin2hex(random_bytes(16))); 
                    $user->setRoles(['ROLE_USER']);
                    
                    $this->entityManager->persist($user);
                }

               
                $user->setGoogleId($googleUser->getId());
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
       
        $url = $this->router->generate('cinema_homepage');

        return new RedirectResponse($url);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response("Désolé, la connexion Google a échoué : " . $message, Response::HTTP_FORBIDDEN);
    }
}