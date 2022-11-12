<?php

namespace App\Service;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Message;
use App\Entity\User;
use App\Event\Notification\MessageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

class MessagingService
{
    public function __construct(readonly private Security                 $security,
                                readonly private EntityManagerInterface   $em,
                                readonly private EmailSenderService       $emailSender,
                                readonly private EventDispatcherInterface $dispatcher,
                                readonly private IriConverterInterface    $iriConverter
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function sendEmailMessage(string $receiverIri, string $content, string $subject): void
    {
        $user = $this->security->getUser();
        $receiver = $this->iriConverter->getResourceFromIri($receiverIri);

        if ($user === $receiver) {
            throw new AccessDeniedHttpException('Sending emails to  yourself is forbidden');
        }


        $message = new Message();
        $message->setReceiver($receiver);
        $message->setAuthor($user);
        $message->setSubject('Outsource me: ' . $subject);
        $message->setContent($content);

        $this->em->persist($message);
        $this->em->flush();

        $this->emailSender->sendMessageEmail($user, $message);

        $this->dispatcher->dispatch(new MessageEvent($message));
    }


}