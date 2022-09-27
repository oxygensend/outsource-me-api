<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleOAuth2Authenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(private readonly ClientRegistry $clientRegistry,
                                private readonly EntityManagerInterface $em,
                                private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            userBadge: new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {

                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                $email = $googleUser->getEmail();

                $existingUser = $this->em->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);
                if ($existingUser) {
                    return $existingUser;
                }

                // If there is a match
                $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $existingUser->setGoogleId($googleUser->getId());
                }

                // Create new one user
                $newUser = new User();
                $newUser->setName($googleUser->getFirstName());
                $newUser->setSurname($googleUser->getLastName());
                $newUser->setEmail($googleUser->getEmail());
                $newUser->setEmailConfirmedAt(new \DateTime());
                $newUser->setGoogleId($googleUser->getId());


                $this->em->persist($newUser);
                $this->em->flush();

                return $newUser;

            })
        );

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->parameterBag->get('redirect_after_oauth2'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }


}