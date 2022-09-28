<?php

namespace App\Service;

use App\Entity\ConfirmationToken;
use App\Entity\User;
use PHPUnit\Util\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailSenderService
{

    public function __construct(private readonly MailerInterface $mailer,
                                private readonly VerifyEmailHelperInterface $verifyEmailHelper,
                                private readonly LoggerInterface $logger,
                                private readonly ParameterBagInterface $parameterBag,
                                private readonly RequestStack $requestStack)
    {
    }


    /**
     * @throws \Exception
     */
    public function sendRegistrationConfirmationEmail(User $user, ConfirmationToken $token): void
    {
        try {


            $this->sendMail(
                $user,
                'Outsource me - registration confirmation',
                'email/registration_confirmation.html.twig',
                [
                    'name' => $user->getName(),
                    'schemeAndHttpHost' => $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost(),
                    'tokenType' => $token->getType(),
                    'token' => $token->getToken(),
                    'tokenExpiredDate' => $token->getExpiredAt()
                ]
            );

        } catch (TransportExceptionInterface $e) {
            $this->logger->warning('EmailSenderService::sendRegistrationConfirmationEmail - unable to send', [
                'user' => $user->getId(), 'message' => $e->getMessage()
            ]);

            throw new \Exception('Unable to send, transport failed', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

    }

    /**
     * @throws \Exception
     */
    public function sendResetPasswordLinkEmail(User $user, ConfirmationToken $token): void
    {
        try {
           $this->sendMail(
               $user,
               'Outsource me - reset password',
               'email/reset_password.html.twig',
               [
                   'name' => $user->getName(),
                   'schemeAndHttpHost' => $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost(),
                   'tokenType' => $token->getType(),
                   'token' => $token->getToken(),
                   'tokenExpiredDate' => $token->getExpiredAt()
               ]
           );
        } catch (TransportExceptionInterface $e){
           $this->logger->warning('EmailSenderService:sendResetPasswordLinkEmail - unable to send', [
               'user' => $user->getId(), 'message' => $e->getMessage()
           ]);

           throw  new \Exception('Unable to send, transport failed', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendMail(User $recipient, string $subject, string $htmlTemplate, array $htmlTemplateVariables = []): void
    {

        $message = (new TemplatedEmail())
            ->from(new Address(
                $this->parameterBag->get('mailer_from_address'),
                $this->parameterBag->get('mailer_from_name')
            ))
            ->to($recipient->getEmail())
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->context($htmlTemplateVariables);

        $this->mailer->send($message);
    }

}