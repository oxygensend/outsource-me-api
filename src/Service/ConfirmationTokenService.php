<?php

namespace App\Service;

use App\Entity\ConfirmationToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ConfirmationTokenService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ParameterBagInterface $parameterBag,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function processToken(string $token, string $type): Response
    {
        $confirmationTokens = $this->em->getRepository(ConfirmationToken::class)->findValidConfirmationTokens($token, $type);

        if (count($confirmationTokens) === 0) {
            $this->logger->warning('Invalid or expired confirmation token.', ['type' => $type, 'token' => $token]);
            throw new UnauthorizedHttpException('Unauthorized.', 'Invalid or expired confirmation token.');
        }

        /** @var ConfirmationToken $confirmationToken */
        $confirmationToken = array_pop($confirmationTokens);
        $user = $confirmationToken->getUser();

        switch ($type) {
            case ConfirmationToken::REGISTRATION_TYPE:
                return $this->processRegistration($confirmationToken, $user);
            case ConfirmationToken::RESET_PASSWORD_TYPE:
                return $this->processResetPasswordSendLink($confirmationToken, $user);
            default:
                $this->logger->error('No code path for token type.', ['confirmationTokenId' => $confirmationToken->getId(), 'type' => $type]);
                throw new UnauthorizedHttpException('Unauthorized.', 'Invalid or expired confirmation token.');
        }
    }


    private function processRegistration(ConfirmationToken $token, User $user): RedirectResponse
    {
        if ($token->getType() !== ConfirmationToken::REGISTRATION_TYPE) {
            throw new BadRequestHttpException('Incorrect token type.');
        }

        if($user->getEmailConfirmedAt() !== null){
            throw new UnauthorizedHttpException('Unauthorized', 'Your email is confirmed');
        }

        $executeToken = $this->findOrCreateConfirmationToken($user, $token->getType());

        $redirectToUrl = str_replace(
            ['{token}', '{type}'],
            [$executeToken->getToken(), $executeToken->getType()],
            $this->parameterBag->get('redirect_after_confirmation')
        );

        $user->setEmailConfirmedAt(new \DateTime());

        $this->em->getRepository(ConfirmationToken::class)->removeUserTokensOfType($user, $token->getType());
        $this->em->flush();

        return new RedirectResponse($redirectToUrl);
    }

    private function processResetPasswordSendLink(ConfirmationToken $token, User $user): RedirectResponse
    {

        if ($token->getType() !== ConfirmationToken::RESET_PASSWORD_TYPE) {
            throw new BadRequestHttpException('Incorrect token type.');
        }

        $newToken = $this->findOrCreateConfirmationToken($user, ConfirmationToken::RESET_PASSWORD_EXECUTE_TYPE);
        $this->em->getRepository(ConfirmationToken::class)->removeUserTokensOfType($user, ConfirmationToken::RESET_PASSWORD_TYPE);


        $redirectToUrl = str_replace(
            ['{token}', '{type}'],
            [$newToken->getToken(), $newToken->getType()],
            $this->parameterBag->get('redirect_after_confirmation')
        );

        return new RedirectResponse($redirectToUrl);
    }


    public function findOrCreateConfirmationToken(User $user, string $type): ConfirmationToken
    {
        $tokens = $this->em->getRepository(ConfirmationToken::class)->findValidUserConfirmationTokens($user, $type);

        if (count($tokens) === 0) {
            $token = new ConfirmationToken();
            $token->setUser($user);
            $token->setType($type);
            $token->setToken(Uuid::uuid4());

            $this->em->persist($token);
        } else {
            $token = array_pop($tokens);
        }

        $token->setExpiredAt(new \DateTime('+7 days'));

        $this->em->flush($token);

        return $token;
    }
}